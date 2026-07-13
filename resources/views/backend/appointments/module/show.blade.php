<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
    @include('components.backend.appointment-styles')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Appointment #{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-appointments.index') }}">Appointments</a></li>
                            <li class="breadcrumb-item active">#{{ $appointment->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-3">
                {{-- LEFT: details --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $appointment->owner_name }}</h5>
                            @if($appointment->status)
                                <span class="status-badge" style="background:{{ $appointment->status->color }};">{{ $appointment->status->name }}</span>
                            @else
                                <span class="status-badge status-badge--muted">Pending</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Owner Name</small>{{ $appointment->owner_name }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Mobile</small><a href="tel:+91{{ $appointment->mobile }}">+91 {{ $appointment->mobile }}</a></div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Email</small><a href="mailto:{{ $appointment->email }}">{{ $appointment->email }}</a></div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Pincode</small>{{ $appointment->pincode }}</div>
                                <div class="col-12 mb-3"><small class="text-muted d-block">Address</small>{{ $appointment->address }}</div>
                                <hr>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Pet Name</small>{{ $appointment->pet_name }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Pet Type / Gender</small>{{ ucfirst($appointment->pet_type) }} · {{ ucfirst($appointment->pet_gender) }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Pet Age</small>{{ $appointment->pet_age ?: '—' }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Consultation</small>{{ $appointment->consult_type === 'first' ? 'First-time Consultation' : 'Follow-up Visit' }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Preferred Date</small>{{ optional($appointment->appointment_date)->format('d M Y') }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Submitted</small>{{ optional($appointment->created_at)->format('d M Y, h:i A') }}</div>
                                <div class="col-12"><small class="text-muted d-block">Reason</small>{{ $appointment->reason }}</div>
                            </div>
                            @if($appointment->appointmentUser)
                                <a href="{{ route('manage-appointment-users.show', $appointment->appointmentUser->id) }}" class="btn btn-sm btn-outline-primary mt-2">View Full Client History</a>
                            @endif
                        </div>
                    </div>

                    {{-- Status timeline --}}
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Status History</h5></div>
                        <div class="card-body">
                            @forelse($appointment->statusHistories as $h)
                                <div class="status-hist">
                                    <span class="status-hist__dot" style="background:{{ optional($h->toStatus)->color ?? '#6b7280' }};"></span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                @if($h->fromStatus)
                                                    <span class="status-badge status-badge--soft">{{ $h->fromStatus->name }}</span>
                                                    <span class="status-hist__arrow">&rarr;</span>
                                                @endif
                                                <span class="status-badge" style="background:{{ optional($h->toStatus)->color ?? '#6b7280' }};">{{ optional($h->toStatus)->name ?? '—' }}</span>
                                            </div>
                                            <span class="status-hist__time">{{ optional($h->created_at)->format('d M Y, h:i A') }}</span>
                                        </div>
                                        <div class="status-hist__by">
                                            By <strong>{{ $h->changed_by_name ?: (optional($h->changedBy)->name ?? 'System') }}</strong>
                                        </div>
                                        @if($h->note)
                                            <div class="status-note">{{ $h->note }}</div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No status changes recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- RIGHT: update status + actions --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Update Status</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('manage-appointments.update-status', $appointment->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">New Status <span class="text-danger">*</span></label>
                                    <select name="appointment_status_id" class="form-select" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" {{ (int) $appointment->appointment_status_id === (int) $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Note / Reason <span class="text-muted">(optional)</span></label>
                                    <textarea name="note" class="form-control" rows="3" maxlength="2000" placeholder="Why is this status being changed?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Save Status</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Quick Actions</h5></div>
                        <div class="card-body d-grid gap-2">
                            <a href="tel:+91{{ $appointment->mobile }}" class="btn btn-outline-primary">Call Owner</a>
                            <a href="mailto:{{ $appointment->email }}" class="btn btn-outline-secondary">Email Owner</a>
                            <a href="{{ route('manage-appointments.index') }}" class="btn btn-outline-secondary">Back to List</a>
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
