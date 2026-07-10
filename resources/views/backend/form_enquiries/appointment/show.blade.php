<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $initials = collect(explode(' ', trim($enquiry->owner_name)))
            ->filter()
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Appointment Enquiry</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-appointment-enquiries.index') }}">Appointment Enquiries</a></li>
                            <li class="breadcrumb-item active">#{{ $enquiry->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid enq-page">

            {{-- Header --}}
            <div class="enq-header">
                <div class="enq-avatar">{{ $initials ?: '?' }}</div>
                <div class="enq-header-meta">
                    <h1>{{ $enquiry->owner_name }}</h1>
                    <p class="enq-header-sub">
                        <span>{{ $enquiry->pet_name }} ({{ ucfirst($enquiry->pet_type) }} · {{ ucfirst($enquiry->pet_gender) }})</span>
                        <span class="enq-sub-dot">·</span>
                        <span>{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
                <div class="enq-header-ref">
                    #{{ str_pad($enquiry->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="row g-3">
                {{-- LEFT: Details --}}
                <div class="col-lg-8">

                    <div class="enq-card">
                        <div class="enq-card-title">Owner Information</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Full Name</span>
                                <div class="enq-value">{{ $enquiry->owner_name }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Email Address</span>
                                <div class="enq-value"><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Mobile Number</span>
                                <div class="enq-value"><a href="tel:+91{{ $enquiry->mobile }}">+91 {{ $enquiry->mobile }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">PIN Code</span>
                                <div class="enq-value">{{ $enquiry->pincode }}</div>
                            </div>
                            <div class="enq-field enq-field-full">
                                <span class="enq-label">Address</span>
                                <div class="enq-value">{{ $enquiry->address }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Pet Details</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Pet Name</span>
                                <div class="enq-value">{{ $enquiry->pet_name }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Pet Type</span>
                                <div class="enq-value">{{ ucfirst($enquiry->pet_type) }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Gender</span>
                                <div class="enq-value">{{ ucfirst($enquiry->pet_gender) }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Age / DOB</span>
                                <div class="enq-value">{{ $enquiry->pet_age ?: '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Consultation</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Type</span>
                                <div class="enq-value">{{ $enquiry->consult_type === 'first' ? 'First-time Consultation' : 'Follow-up Visit' }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Preferred Date</span>
                                <div class="enq-value">{{ optional($enquiry->appointment_date)->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Reason for Consultation</div>
                        <p class="enq-message-body">{{ $enquiry->reason }}</p>
                    </div>

                </div>

                {{-- RIGHT: Actions + metadata --}}
                <div class="col-lg-4">

                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="tel:+91{{ $enquiry->mobile }}" class="btn btn-primary">Call Owner</a>
                            <a href="mailto:{{ $enquiry->email }}?subject=Re: Appointment Request - {{ urlencode($enquiry->pet_name) }}" class="btn btn-outline-secondary">Reply via Email</a>
                            <a href="{{ route('manage-appointment-enquiries.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Submission Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Enquiry ID</span><span class="v">#{{ str_pad($enquiry->id, 4, '0', STR_PAD_LEFT) }}</span></li>
                            <li><span class="k">Submitted</span><span class="v">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</span></li>
                            <li><span class="k">Preferred Date</span><span class="v">{{ optional($enquiry->appointment_date)->format('d M Y') }}</span></li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
