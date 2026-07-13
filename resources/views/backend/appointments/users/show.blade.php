<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
    @include('components.backend.appointment-styles')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $initials = collect(explode(' ', trim((string) ($user->name ?: 'Guest'))))
            ->filter()
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Client History</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-appointment-users.index') }}">Appointment Users</a></li>
                            <li class="breadcrumb-item active">#{{ $user->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-3">
                {{-- LEFT: profile --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                 style="width:72px;height:72px;background:#eef2ff;color:#4f46e5;font-size:26px;font-weight:700;">
                                {{ $initials ?: '?' }}
                            </div>
                            <h5 class="mb-1">{{ $user->name ?: 'Unnamed Client' }}</h5>
                            <p class="text-muted mb-3">+91 {{ $user->mobile }}</p>
                            <span class="badge bg-info">{{ $user->appointments->count() }} Appointment(s)</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span><span>{{ $user->email ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Pincode</span><span>{{ $user->pincode ?: '—' }}</span></li>
                            <li class="list-group-item"><span class="text-muted d-block mb-1">Address</span><span>{{ $user->address ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">First Seen</span><span>{{ optional($user->created_at)->format('d M Y') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Last Verified</span><span>{{ optional($user->last_verified_at)->format('d M Y, h:i A') ?: '—' }}</span></li>
                        </ul>
                    </div>
                    <a href="{{ route('manage-appointment-users.index') }}" class="btn btn-outline-secondary w-100">Back to List</a>
                </div>

                {{-- RIGHT: appointment history --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Appointment History</h5>
                            <span class="badge bg-primary">{{ $user->appointments->count() }}</span>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Appt. Date</th>
                                        <th>Consultation</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th class="text-end">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->appointments as $appt)
                                        <tr>
                                            <td>{{ optional($appt->appointment_date)->format('d M Y') }}</td>
                                            <td>{{ $appt->consult_type === 'first' ? 'First-time' : 'Follow-up' }}</td>
                                            <td>
                                                @if($appt->status)
                                                    <span class="status-badge" style="background:{{ $appt->status->color }};">{{ $appt->status->name }}</span>
                                                @else
                                                    <span class="status-badge" style="background:#6b7280;">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($appt->created_at)->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('manage-appointments.show', $appt->id) }}" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">This client has no appointments yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
