{{-- AJAX-swappable results: count + table + pagination. --}}
<div class="d-flex align-items-center mb-2">
    <span class="badge bg-primary">{{ $appointments->total() }} Appointment(s)</span>
</div>

<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Appt. Date</th>
                <th>Owner</th>
                <th>Mobile</th>
                <th>Consultation</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
                <tr>
                    <td>#{{ str_pad($appt->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ optional($appt->appointment_date)->format('d M Y') }}</td>
                    <td>{{ $appt->owner_name }}</td>
                    <td>+91 {{ $appt->mobile }}</td>
                    <td>{{ $appt->consult_type === 'first' ? 'First-time' : 'Follow-up' }}</td>
                    <td>
                        @if($appt->status)
                            <span class="status-badge" style="background:{{ $appt->status->color }};">{{ $appt->status->name }}</span>
                        @else
                            <span class="status-badge status-badge--muted">Pending</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 js-update-status"
                                data-id="{{ $appt->id }}"
                                data-ref="#{{ str_pad($appt->id, 4, '0', STR_PAD_LEFT) }}"
                                data-owner="{{ $appt->owner_name }}"
                                data-status="{{ $appt->appointment_status_id }}"
                                data-bs-toggle="modal" data-bs-target="#statusModal">
                            Status
                        </button>
                        <a href="{{ route('manage-appointments.show', $appt->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No appointments match the current filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $appointments->links() }}
</div>
