<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Status Change Log</h5>
        <span class="badge bg-primary">{{ $rows->total() }} records</span>
    </div>
    <div class="card-body">
        <div class="table-responsive custom-scrollbar">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr><th>Appt. Ref</th><th>Change</th><th>Changed By</th><th>Note</th><th>When</th></tr>
                </thead>
                <tbody>
                    @forelse($rows as $h)
                        <tr>
                            <td><a href="{{ route('manage-appointments.show', $h->appointment_enquiry_id) }}">#{{ str_pad($h->appointment_enquiry_id, 4, '0', STR_PAD_LEFT) }}</a></td>
                            <td class="text-nowrap">
                                @if($h->fromStatus)<span class="status-badge status-badge--soft">{{ $h->fromStatus->name }}</span> <span class="status-hist__arrow">&rarr;</span>@endif
                                <span class="status-badge" style="background:{{ optional($h->toStatus)->color ?? '#6b7280' }};">{{ optional($h->toStatus)->name ?? '—' }}</span>
                            </td>
                            <td>{{ $h->changed_by_name ?: 'System' }}</td>
                            <td>{{ $h->note ? \Illuminate\Support\Str::limit($h->note, 60) : '—' }}</td>
                            <td class="text-nowrap">{{ optional($h->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No status changes match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div>
</div>
