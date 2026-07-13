{{-- AJAX-swappable results: table + pagination. --}}
<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>When</th>
                <th>Channel</th>
                <th>Type</th>
                <th>Recipient</th>
                <th>Status</th>
                <th>Triggered By</th>
                <th class="text-end">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap">{{ optional($log->created_at)->format('d M Y, h:i A') }}</td>
                    <td><span class="badge {{ $log->channelBadgeClass() }} text-uppercase">{{ $log->channel }}</span></td>
                    <td>{{ $log->typeLabel() }}</td>
                    <td>
                        <div>{{ $log->recipient }}</div>
                        @if($log->recipient_name)<div class="text-muted small">{{ $log->recipient_name }}</div>@endif
                    </td>
                    <td><span class="badge {{ $log->statusBadgeClass() }} text-uppercase">{{ $log->status }}</span></td>
                    <td>{{ $log->triggered_by_name ?: 'System / Website' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.communication-logs.show', $log->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No communication records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
