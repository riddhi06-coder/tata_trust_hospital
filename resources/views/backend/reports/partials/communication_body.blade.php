<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Messages</h5>
        <span class="badge bg-primary">{{ $rows->total() }} records</span>
    </div>
    <div class="card-body">
        <div class="table-responsive custom-scrollbar">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr><th>When</th><th>Channel</th><th>Type</th><th>Recipient</th><th>Status</th><th class="text-end">Details</th></tr>
                </thead>
                <tbody>
                    @forelse($rows as $l)
                        <tr>
                            <td class="text-nowrap">{{ optional($l->created_at)->format('d M Y, h:i A') }}</td>
                            <td><span class="badge {{ $l->channelBadgeClass() }} text-uppercase">{{ $l->channel }}</span></td>
                            <td>{{ $l->typeLabel() }}</td>
                            <td>
                                <div>{{ $l->recipient }}</div>
                                @if($l->recipient_name)<div class="text-muted small">{{ $l->recipient_name }}</div>@endif
                            </td>
                            <td><span class="badge {{ $l->statusBadgeClass() }} text-uppercase">{{ $l->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.communication-logs.show', $l->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No messages match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div>
</div>
