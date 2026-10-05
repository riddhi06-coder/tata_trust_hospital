{{-- AJAX-swappable results: notifications list + pagination. --}}
<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th style="width:40px;"></th>
                <th>Notification</th>
                <th>Source</th>
                <th>When</th>
                <th>Status</th>
                <th class="text-end">Open</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $n)
                <tr class="{{ is_null($n->read_at) ? 'table-active fw-semibold' : '' }}">
                    <td class="text-center">
                        <span class="badge rounded-circle p-2 bg-{{ $n->color ?: 'primary' }}">&nbsp;</span>
                    </td>
                    <td>
                        <div>{{ $n->title }}</div>
                        @if($n->body)<div class="text-muted small fw-normal">{{ $n->body }}</div>@endif
                    </td>
                    <td><span class="badge {{ $n->source === 'whatsapp' ? 'bg-success' : 'bg-info' }} text-uppercase">{{ $n->source }}</span></td>
                    <td class="text-nowrap">{{ optional($n->created_at)->format('d M Y, h:i A') }}</td>
                    <td>
                        @if($n->read_at)
                            <span class="badge bg-light text-dark" title="Read by {{ $n->read_by_name }}">Read</span>
                        @else
                            <span class="badge bg-danger">Unread</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.notifications.go', $n->id) }}" class="btn btn-sm btn-primary py-1 px-2">Open</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No notifications found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $notifications->links() }}</div>
