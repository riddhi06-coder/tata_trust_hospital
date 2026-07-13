<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppointmentEnquiry;
use App\Models\AppointmentStatus;
use App\Models\AppointmentStatusHistory;
use App\Models\AppointmentUser;
use App\Models\CommunicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /** Reports landing page (cards). */
    public function index()
    {
        $isSuperAdmin = optional(Auth::user())->isSuperAdmin();

        return view('backend.reports.index', compact('isSuperAdmin'));
    }

    /* ===================================================================== */
    /*  1. APPOINTMENTS REPORT                                                */
    /* ===================================================================== */

    public function appointments(Request $request)
    {
        return view('backend.reports.appointments', $this->appointmentsData($request));
    }

    public function appointmentsFilter(Request $request)
    {
        return view('backend.reports.partials.appointments_body', $this->appointmentsData($request))->render();
    }

    public function appointmentsExport(Request $request): StreamedResponse
    {
        $rows = $this->appointmentsQuery($request)->orderByDesc('appointment_date')->orderByDesc('id')->get();

        return $this->streamCsv('appointments-report', [
            'Ref', 'Appointment Date', 'Status', 'Owner', 'Mobile', 'Email',
            'Pincode', 'Pet', 'Pet Type', 'Gender', 'Consultation', 'Submitted',
        ], function ($out) use ($rows) {
            foreach ($rows as $a) {
                fputcsv($out, [
                    '#'.str_pad($a->id, 4, '0', STR_PAD_LEFT),
                    optional($a->appointment_date)->format('d M Y'),
                    $a->status->name ?? 'Pending',
                    $a->owner_name, $a->mobile, $a->email, $a->pincode,
                    $a->pet_name, ucfirst((string) $a->pet_type), ucfirst((string) $a->pet_gender),
                    $a->consult_type === 'first' ? 'First-time' : 'Follow-up',
                    optional($a->created_at)->format('d M Y, h:i A'),
                ]);
            }
        });
    }

    private function appointmentsQuery(Request $request)
    {
        $q = AppointmentEnquiry::whereNull('deleted_by')
            ->with('status')
            ->when($request->filled('status'), fn ($q) => $q->where('appointment_status_id', $request->input('status')))
            ->when($request->filled('pet_type'), fn ($q) => $q->where('pet_type', $request->input('pet_type')))
            ->when($request->filled('consult_type'), fn ($q) => $q->where('consult_type', $request->input('consult_type')));

        return $this->applyPeriod($q, $request, 'appointment_date');
    }

    private function appointmentsData(Request $request): array
    {
        $rows = $this->appointmentsQuery($request)
            ->orderByDesc('appointment_date')->orderByDesc('id')
            ->paginate(20)->withPath(route('admin.reports.appointments'))->withQueryString();

        $statuses = AppointmentStatus::whereNull('deleted_by')->orderBy('sort_order')->orderBy('name')->get();
        $years    = $this->yearsFrom(AppointmentEnquiry::whereNull('deleted_by'), 'appointment_date');

        $filters = $this->periodFilters($request) + [
            'status'       => $request->input('status', ''),
            'pet_type'     => $request->input('pet_type', ''),
            'consult_type' => $request->input('consult_type', ''),
        ];

        return compact('rows', 'statuses', 'years', 'filters');
    }

    /* ===================================================================== */
    /*  2. OPERATIONAL REPORT (status-change history)                        */
    /* ===================================================================== */

    public function operational(Request $request)
    {
        return view('backend.reports.operational', $this->operationalData($request));
    }

    public function operationalFilter(Request $request)
    {
        return view('backend.reports.partials.operational_body', $this->operationalData($request))->render();
    }

    public function operationalExport(Request $request): StreamedResponse
    {
        $rows = $this->operationalQuery($request)->orderByDesc('id')->get();

        return $this->streamCsv('operational-report', [
            'Appointment Ref', 'From Status', 'To Status', 'Changed By', 'Note', 'When',
        ], function ($out) use ($rows) {
            foreach ($rows as $h) {
                fputcsv($out, [
                    '#'.str_pad($h->appointment_enquiry_id, 4, '0', STR_PAD_LEFT),
                    optional($h->fromStatus)->name ?? '—',
                    optional($h->toStatus)->name ?? '—',
                    $h->changed_by_name ?: 'System',
                    $h->note,
                    optional($h->created_at)->format('d M Y, h:i A'),
                ]);
            }
        });
    }

    private function operationalQuery(Request $request)
    {
        $q = AppointmentStatusHistory::query()
            ->with(['fromStatus', 'toStatus'])
            ->when($request->filled('to_status'), fn ($q) => $q->where('to_status_id', $request->input('to_status')));

        return $this->applyPeriod($q, $request, 'created_at');
    }

    private function operationalData(Request $request): array
    {
        $rows = $this->operationalQuery($request)
            ->orderByDesc('id')
            ->paginate(20)->withPath(route('admin.reports.operational'))->withQueryString();

        $toStatuses = AppointmentStatus::whereNull('deleted_by')->orderBy('sort_order')->orderBy('name')->get();
        $years      = $this->yearsFrom(AppointmentStatusHistory::query(), 'created_at');

        $filters = $this->periodFilters($request) + ['to_status' => $request->input('to_status', '')];

        return compact('rows', 'toStatuses', 'years', 'filters');
    }

    /* ===================================================================== */
    /*  3. CLIENTS REPORT                                                     */
    /* ===================================================================== */

    public function clients(Request $request)
    {
        return view('backend.reports.clients', $this->clientsData($request));
    }

    public function clientsFilter(Request $request)
    {
        return view('backend.reports.partials.clients_body', $this->clientsData($request))->render();
    }

    public function clientsExport(Request $request): StreamedResponse
    {
        $rows = $this->clientsQuery($request)->orderByDesc('appointments_count')->get();

        return $this->streamCsv('clients-report', [
            'Name', 'Mobile', 'Email', 'Pincode', 'Appointments', 'Registered', 'Last Verified',
        ], function ($out) use ($rows) {
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->name, $c->mobile, $c->email, $c->pincode,
                    $c->appointments_count,
                    optional($c->created_at)->format('d M Y'),
                    optional($c->last_verified_at)->format('d M Y, h:i A'),
                ]);
            }
        });
    }

    private function clientsQuery(Request $request)
    {
        $q = AppointmentUser::whereNull('deleted_by')
            ->withCount('appointments')
            ->when($request->filled('visits'), function ($q) use ($request) {
                $request->input('visits') === 'repeat'
                    ? $q->having('appointments_count', '>', 1)
                    : ($request->input('visits') === 'one'
                        ? $q->having('appointments_count', '=', 1)
                        : $q->having('appointments_count', '=', 0));
            });

        return $this->applyPeriod($q, $request, 'created_at');
    }

    private function clientsData(Request $request): array
    {
        $rows = $this->clientsQuery($request)
            ->orderByDesc('appointments_count')->orderByDesc('id')
            ->paginate(20)->withPath(route('admin.reports.clients'))->withQueryString();

        $years = $this->yearsFrom(AppointmentUser::whereNull('deleted_by'), 'created_at');

        $filters = $this->periodFilters($request) + ['visits' => $request->input('visits', '')];

        return compact('rows', 'years', 'filters');
    }

    /* ===================================================================== */
    /*  4. COMMUNICATION REPORT (Super Admin only)                           */
    /* ===================================================================== */

    public function communication(Request $request)
    {
        $this->authorizeSuperAdmin();

        return view('backend.reports.communication', $this->communicationData($request));
    }

    public function communicationFilter(Request $request)
    {
        $this->authorizeSuperAdmin();

        return view('backend.reports.partials.communication_body', $this->communicationData($request))->render();
    }

    public function communicationExport(Request $request): StreamedResponse
    {
        $this->authorizeSuperAdmin();

        $rows = $this->communicationQuery($request)->orderByDesc('id')->get();

        return $this->streamCsv('communication-report', [
            'When', 'Channel', 'Type', 'Recipient', 'Recipient Name', 'Status', 'Error', 'Triggered By',
        ], function ($out) use ($rows) {
            foreach ($rows as $l) {
                fputcsv($out, [
                    optional($l->created_at)->format('d M Y, h:i A'),
                    strtoupper($l->channel), $l->typeLabel(),
                    $l->recipient, $l->recipient_name, strtoupper($l->status),
                    $l->error, $l->triggered_by_name ?: 'System / Website',
                ]);
            }
        });
    }

    private function communicationQuery(Request $request)
    {
        $q = CommunicationLog::query()
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->input('channel')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));

        return $this->applyPeriod($q, $request, 'created_at');
    }

    private function communicationData(Request $request): array
    {
        $rows = $this->communicationQuery($request)
            ->orderByDesc('id')
            ->paginate(20)->withPath(route('admin.reports.communication'))->withQueryString();

        $types = CommunicationLog::query()->select('type')->distinct()->orderBy('type')->pluck('type')->filter()->values();
        $years = $this->yearsFrom(CommunicationLog::query(), 'created_at');

        $filters = $this->periodFilters($request) + [
            'channel' => $request->input('channel', ''),
            'type'    => $request->input('type', ''),
            'status'  => $request->input('status', ''),
        ];

        return compact('rows', 'types', 'years', 'filters');
    }

    /* ===================================================================== */
    /*  Helpers                                                               */
    /* ===================================================================== */

    /**
     * Apply period filtering. A custom From/To range takes precedence; otherwise
     * the Year and/or Month selectors are used. Nothing selected = all records.
     */
    private function applyPeriod($query, Request $request, string $column)
    {
        if ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->whereDate($column, '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate($column, '<=', $request->input('date_to'));
            }

            return $query;
        }

        if ($request->filled('year')) {
            $query->whereYear($column, (int) $request->input('year'));
        }
        if ($request->filled('month')) {
            $query->whereMonth($column, (int) $request->input('month'));
        }

        return $query;
    }

    private function periodFilters(Request $request): array
    {
        return [
            'year'      => $request->input('year', ''),
            'month'     => $request->input('month', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to'   => $request->input('date_to', ''),
        ];
    }

    /** Distinct years present in a table's date column, newest first. */
    private function yearsFrom($base, string $column): array
    {
        return $base->clone()
            ->selectRaw("YEAR($column) as y")
            ->whereNotNull($column)
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->filter()
            ->values()
            ->all();
    }

    private function streamCsv(string $basename, array $columns, callable $rowWriter): StreamedResponse
    {
        $filename = $basename.'-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($columns, $rowWriter) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $rowWriter($out);
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(optional(Auth::user())->isSuperAdmin(), 403, 'Only the Super Admin can view this report.');
    }
}
