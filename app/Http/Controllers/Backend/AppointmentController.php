<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppointmentEnquiry;
use App\Models\AppointmentStatus;
use App\Models\AppointmentStatusHistory;
use App\Services\MessageIndiaSms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppointmentController extends Controller
{
    /**
     * Appointments module — full data with filters + status management.
     */
    public function index(Request $request)
    {
        $appointments = $this->paginatedResults($request);

        $statuses = AppointmentStatus::whereNull('deleted_by')
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        $filters = $this->currentFilters($request);

        return view('backend.appointments.module.index', compact('appointments', 'statuses', 'filters'));
    }

    /**
     * AJAX endpoint — returns just the results partial (count + table + pagination)
     * so filtering never reloads the page.
     */
    public function filter(Request $request)
    {
        $appointments = $this->paginatedResults($request);

        return view('backend.appointments.module._table', compact('appointments'))->render();
    }

    /**
     * Single appointment — details + full status-change timeline.
     */
    public function show($id)
    {
        $appointment = AppointmentEnquiry::whereNull('deleted_by')
            ->with([
                'status',
                'appointmentUser',
                'statusHistories' => fn ($q) => $q->with(['fromStatus', 'toStatus', 'changedBy']),
            ])
            ->findOrFail($id);

        $statuses = AppointmentStatus::whereNull('deleted_by')
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('backend.appointments.module.show', compact('appointment', 'statuses'));
    }

    /**
     * Update the status of an appointment, recording who / when / why.
     */
    public function updateStatus(Request $request, $id, MessageIndiaSms $sms)
    {
        $appointment = AppointmentEnquiry::whereNull('deleted_by')->findOrFail($id);

        $toStatus     = AppointmentStatus::whereNull('deleted_by')->find($request->appointment_status_id);
        $requiresDate = $toStatus && $toStatus->requires_appointment_date;
        $currentDate  = optional($appointment->appointment_date)->toDateString();

        $rules = [
            'appointment_status_id' => ['required', 'integer', 'exists:appointment_statuses,id'],
            'note'                  => ['nullable', 'string', 'max:2000'],
        ];

        // Statuses flagged as "requires a new date" (e.g. Rescheduled) must supply
        // a future date that differs from the current appointment date.
        if ($requiresDate) {
            $rules['appointment_date'] = ['required', 'date', 'after_or_equal:today'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'appointment_status_id.required'  => 'Please choose a status.',
            'appointment_status_id.exists'    => 'The selected status is invalid.',
            'appointment_date.required'       => 'A new appointment date is required to reschedule.',
            'appointment_date.after_or_equal' => 'The new appointment date cannot be in the past.',
        ]);

        if ($requiresDate) {
            $validator->after(function ($v) use ($request, $currentDate) {
                $new = $request->input('appointment_date');
                if ($new && $currentDate && $new === $currentDate) {
                    $v->errors()->add(
                        'appointment_date',
                        'The new date must be different from the current appointment date ('.Carbon::parse($currentDate)->format('d M Y').').'
                    );
                }
            });
        }

        $validator->validate();

        $fromStatusId = $appointment->appointment_status_id;
        $toStatusId   = (int) $request->appointment_status_id;

        $note   = $request->input('note') ?: null;
        $oldFmt = $currentDate ? Carbon::parse($currentDate)->format('d M Y') : '—';
        $newFmt = null;

        $updateData = [
            'appointment_status_id' => $toStatusId,
            'updated_by'            => Auth::id(),
            'updated_at'            => Carbon::now(),
        ];

        // On reschedule, move the appointment date and log the change in the note.
        if ($requiresDate) {
            $newFmt = Carbon::parse($request->appointment_date)->format('d M Y');
            $line   = 'Appointment date changed from '.$oldFmt.' to '.$newFmt.'.';
            $note   = $note ? ($line.' '.$note) : $line;

            $updateData['appointment_date'] = $request->appointment_date;
        }

        // Record the change in the immutable history trail.
        AppointmentStatusHistory::create([
            'appointment_enquiry_id' => $appointment->id,
            'from_status_id'         => $fromStatusId,
            'to_status_id'           => $toStatusId,
            'note'                   => $note,
            'changed_by'             => Auth::id(),
            'changed_by_name'        => optional(Auth::user())->name,
            'created_at'             => Carbon::now(),
        ]);

        $appointment->update($updateData);

        // Fire the transactional SMS configured on this status. Never blocks the response.
        try {
            if ($toStatus && $toStatus->sms_trigger === 'reschedule' && $newFmt) {
                $sms->sendAppointmentReschedule($appointment->mobile, $oldFmt, $newFmt);
            } elseif ($toStatus && $toStatus->sms_trigger === 'cancellation') {
                $sms->sendAppointmentCancellation($appointment->mobile, $currentDate ? $oldFmt : 'your scheduled date');
            }
        } catch (\Throwable $e) {
            Log::error('Appointment status SMS failed: '.$e->getMessage(), ['enquiry_id' => $appointment->id]);
        }

        return redirect()
            ->back()
            ->with('message', 'Appointment status updated successfully.');
    }

    /**
     * Stream a CSV of the currently-filtered appointments.
     */
    public function export(Request $request): StreamedResponse
    {
        $appointments = $this->filteredQuery($request)
            ->with(['status'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->get();

        $filename = 'appointments-'.now()->format('Y-m-d-His').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'Ref', 'Submitted On', 'Appointment Date', 'Status',
            'Owner Name', 'Mobile', 'Email', 'Address', 'Pincode',
            'Pet Name', 'Pet Type', 'Pet Gender', 'Pet Age',
            'Consultation', 'Reason',
        ];

        $callback = function () use ($appointments, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);

            foreach ($appointments as $a) {
                fputcsv($out, [
                    '#'.str_pad($a->id, 4, '0', STR_PAD_LEFT),
                    optional($a->created_at)->format('d M Y, h:i A'),
                    optional($a->appointment_date)->format('d M Y'),
                    $a->status->name ?? 'Pending',
                    $a->owner_name,
                    $a->mobile,
                    $a->email,
                    $a->address,
                    $a->pincode,
                    $a->pet_name,
                    ucfirst((string) $a->pet_type),
                    ucfirst((string) $a->pet_gender),
                    $a->pet_age,
                    $a->consult_type === 'first' ? 'First-time' : 'Follow-up',
                    $a->reason,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* --------------------------------------------------------------------- */
    /* Filtering helpers (shared by index + export)                          */
    /* --------------------------------------------------------------------- */

    /** Paginated, filtered result set shared by the full page and the AJAX partial. */
    private function paginatedResults(Request $request)
    {
        return $this->filteredQuery($request)
            ->with(['status', 'appointmentUser'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withPath(route('manage-appointments.index'))
            ->withQueryString();
    }

    private function filteredQuery(Request $request)
    {
        $f = $this->currentFilters($request);

        return AppointmentEnquiry::whereNull('deleted_by')
            ->when($f['status'] !== '', fn ($q) => $q->where('appointment_status_id', $f['status']))
            ->when($f['pet_type'] !== '', fn ($q) => $q->where('pet_type', $f['pet_type']))
            ->when($f['consult_type'] !== '', fn ($q) => $q->where('consult_type', $f['consult_type']))
            ->when($f['date_from'] !== '', fn ($q) => $q->whereDate('appointment_date', '>=', $f['date_from']))
            ->when($f['date_to'] !== '', fn ($q) => $q->whereDate('appointment_date', '<=', $f['date_to']))
            ->when($f['search'] !== '', function ($q) use ($f) {
                $q->where(function ($w) use ($f) {
                    $w->where('owner_name', 'like', "%{$f['search']}%")
                      ->orWhere('mobile', 'like', "%{$f['search']}%")
                      ->orWhere('email', 'like', "%{$f['search']}%")
                      ->orWhere('pet_name', 'like', "%{$f['search']}%");
                });
            });
    }

    private function currentFilters(Request $request): array
    {
        return [
            'status'       => trim((string) $request->input('status', '')),
            'pet_type'     => trim((string) $request->input('pet_type', '')),
            'consult_type' => trim((string) $request->input('consult_type', '')),
            'date_from'    => trim((string) $request->input('date_from', '')),
            'date_to'      => trim((string) $request->input('date_to', '')),
            'search'       => trim((string) $request->input('search', '')),
        ];
    }
}
