{{-- AJAX-swappable results: table + pagination. --}}
<div class="table-responsive custom-scrollbar">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>When</th>
                <th>Name</th>
                <th>WhatsApp</th>
                <th>Message</th>
                <th class="text-end">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($queries as $q)
                <tr>
                    <td class="text-nowrap">{{ optional($q->created_at)->format('d M Y, h:i A') }}</td>
                    <td>{{ $q->name ?: 'WhatsApp User' }}</td>
                    <td class="text-nowrap">{{ $q->wa_id }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($q->message, 70) }}</td>
                    <td class="text-end">
                        <a href="{{ route('manage-whatsapp-queries.show', $q->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No WhatsApp team queries found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $queries->links() }}</div>
