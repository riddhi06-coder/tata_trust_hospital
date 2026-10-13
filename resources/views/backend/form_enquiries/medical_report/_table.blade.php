{{-- AJAX-swappable results: table + pagination. --}}
<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>When</th>
                <th>Client</th>
                <th>Patient</th>
                <th>Patient UID</th>
                <th>Mobile</th>
                <th>Report</th>
                <th class="text-end">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $r)
                <tr>
                    <td class="text-nowrap">{{ optional($r->created_at)->format('d M Y, h:i A') }}</td>
                    <td>{{ $r->client_name ?: '—' }}</td>
                    <td>{{ $r->patient_name ?: '—' }}</td>
                    <td>{{ $r->patient_uid ?: '—' }}</td>
                    <td class="text-nowrap">{{ $r->mobile ?: $r->wa_id }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($r->report_name, 40) ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('manage-medical-reports.show', $r->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No medical report requests found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $reports->links() }}</div>
