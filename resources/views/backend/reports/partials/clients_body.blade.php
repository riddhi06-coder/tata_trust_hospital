<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Clients</h5>
        <span class="badge bg-primary">{{ $rows->total() }} records</span>
    </div>
    <div class="card-body">
        <div class="table-responsive custom-scrollbar">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr><th>Name</th><th>Mobile</th><th>Email</th><th>Appointments</th><th>Registered</th><th class="text-end">History</th></tr>
                </thead>
                <tbody>
                    @forelse($rows as $c)
                        <tr>
                            <td>{{ $c->name ?: '—' }}</td>
                            <td class="text-nowrap">+91 {{ $c->mobile }}</td>
                            <td>{{ $c->email ?: '—' }}</td>
                            <td><span class="badge bg-info">{{ $c->appointments_count }}</span></td>
                            <td class="text-nowrap">{{ optional($c->created_at)->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('manage-appointment-users.show', $c->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No clients match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div>
</div>
