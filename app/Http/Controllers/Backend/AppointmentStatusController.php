<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppointmentStatusController extends Controller
{
    public function index()
    {
        $statuses = AppointmentStatus::whereNull('deleted_by')
            ->withCount('appointments')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('backend.appointments.statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('backend.appointments.statuses.create');
    }

    public function store(Request $request)
    {
        $this->validatePayload($request)->validate();

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            // Only one default status may exist at a time.
            AppointmentStatus::where('is_default', true)->update(['is_default' => false]);
        }

        $smsTrigger   = $this->normalizeSmsTrigger($request->input('sms_trigger'));
        $requiresDate = $request->boolean('requires_appointment_date') || $smsTrigger === 'reschedule';

        AppointmentStatus::create([
            'name'                      => $request->name,
            'slug'                      => $this->uniqueSlug($request->name),
            'color'                     => $request->color ?: '#6b7280',
            'sort_order'                => (int) $request->input('sort_order', 0),
            'is_active'                 => $request->boolean('is_active'),
            'is_default'                => $isDefault,
            'requires_appointment_date' => $requiresDate,
            'sms_trigger'               => $smsTrigger,
            'created_by'                => Auth::id(),
            'created_at'                => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-appointment-statuses.index')
            ->with('message', 'Appointment status added successfully.');
    }

    public function edit($id)
    {
        $status = AppointmentStatus::whereNull('deleted_by')->findOrFail($id);

        return view('backend.appointments.statuses.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        $status = AppointmentStatus::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request)->validate();

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            AppointmentStatus::where('is_default', true)
                ->where('id', '!=', $status->id)
                ->update(['is_default' => false]);
        }

        $slug = $status->slug;
        if ($request->name !== $status->name || empty($slug)) {
            $slug = $this->uniqueSlug($request->name, $status->id);
        }

        $smsTrigger   = $this->normalizeSmsTrigger($request->input('sms_trigger'));
        $requiresDate = $request->boolean('requires_appointment_date') || $smsTrigger === 'reschedule';

        $status->update([
            'name'                      => $request->name,
            'slug'                      => $slug,
            'color'                     => $request->color ?: '#6b7280',
            'sort_order'                => (int) $request->input('sort_order', 0),
            'is_active'                 => $request->boolean('is_active'),
            'is_default'                => $isDefault,
            'requires_appointment_date' => $requiresDate,
            'sms_trigger'               => $smsTrigger,
            'updated_by'                => Auth::id(),
            'updated_at'                => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-appointment-statuses.index')
            ->with('message', 'Appointment status updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $status = AppointmentStatus::whereNull('deleted_by')
                ->withCount('appointments')
                ->findOrFail($id);

            // Guard: never orphan appointments that reference this status.
            if ($status->appointments_count > 0) {
                return redirect()
                    ->route('manage-appointment-statuses.index')
                    ->with('error', 'This status is in use by '.$status->appointments_count.' appointment(s) and cannot be deleted.');
            }

            if ($status->is_default) {
                return redirect()
                    ->route('manage-appointment-statuses.index')
                    ->with('error', 'The default status cannot be deleted. Set another status as default first.');
            }

            $status->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-appointment-statuses.index')
                ->with('message', 'Appointment status deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request)
    {
        return Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'color'      => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Please enter a status name.',
        ]);
    }

    /** Only allow the known SMS trigger keys; anything else means "no SMS". */
    private function normalizeSmsTrigger($value): ?string
    {
        return in_array($value, ['cancellation', 'reschedule'], true) ? $value : null;
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'status-'.uniqid();
        }

        $slug = $base;
        $i    = 1;
        while (
            AppointmentStatus::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
