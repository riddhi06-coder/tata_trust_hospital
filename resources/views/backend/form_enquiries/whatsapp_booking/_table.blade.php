{{-- AJAX-swappable results: table + pagination. --}}
<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>Ref</th>
                <th>When</th>
                <th>Client</th>
                <th>Pet Parent</th>
                <th>Pet</th>
                <th>Reason</th>
                <th>Preferred</th>
                <th class="text-end">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
                <tr>
                    <td class="text-nowrap">#{{ $r->reference() }}</td>
                    <td class="text-nowrap">{{ optional($r->created_at)->format('d M Y, h:i A') }}</td>
                    <td><span class="badge {{ $r->client_type === 'existing' ? 'bg-info' : 'bg-success' }} text-uppercase">{{ $r->client_type }}</span></td>
                    <td>
                        <div>{{ $r->parent_name ?: '—' }}</div>
                        <div class="text-muted small">{{ $r->mobile ?: $r->wa_id }}</div>
                    </td>
                    <td>
                        <div>{{ $r->pet_name ?: '—' }}</div>
                        @if($r->species)<div class="text-muted small">{{ $r->species }}</div>@endif
                    </td>
                    <td>{{ $r->reason ?: '—' }}</td>
                    <td class="text-nowrap">{{ trim(($r->preferred_day ?: '').' '.($r->preferred_time ? '· '.$r->preferred_time : '')) ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('manage-whatsapp-bookings.show', $r->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No WhatsApp booking requests found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $requests->links() }}</div>
