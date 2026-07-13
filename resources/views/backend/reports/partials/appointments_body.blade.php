<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Appointments</h5>
        <span class="badge bg-primary">{{ $rows->total() }} records</span>
    </div>
    <div class="card-body">
        <div class="table-responsive custom-scrollbar">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Ref</th><th>Appt. Date</th><th>Owner</th><th>Mobile</th>
                        <th>Pet</th><th>Consultation</th><th>Status</th><th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $a)
                        <tr>
                            <td>#{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-nowrap">{{ optional($a->appointment_date)->format('d M Y') }}</td>
                            <td>{{ $a->owner_name }}</td>
                            <td class="text-nowrap">+91 {{ $a->mobile }}</td>
                            <td>{{ $a->pet_name }} <span class="text-muted">({{ ucfirst($a->pet_type) }})</span></td>
                            <td>{{ $a->consult_type === 'first' ? 'First-time' : 'Follow-up' }}</td>
                            <td>
                                @if($a->status)<span class="status-badge" style="background:{{ $a->status->color }};">{{ $a->status->name }}</span>
                                @else <span class="status-badge status-badge--muted">Pending</span>@endif
                            </td>
                            <td class="text-nowrap">{{ optional($a->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No appointments match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div>
</div>
