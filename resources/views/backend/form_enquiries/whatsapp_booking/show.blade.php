<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $waDigits = preg_replace('/\D+/', '', $booking->wa_id);
        $fields = [
            'Client Type'    => ucfirst((string) $booking->client_type),
            'Pet Parent'     => $booking->parent_name,
            'Mobile'         => $booking->mobile,
            'Email'          => $booking->email,
            'Address'        => $booking->address,
            'PIN Code'       => $booking->pincode,
            'Pet Name'       => $booking->pet_name,
            'Species'        => $booking->species,
            'Sex'            => $booking->sex,
            'Breed'          => $booking->breed,
            'Colour'         => $booking->colour,
            'DOB / Age'      => $booking->dob_age,
            'Weight'         => $booking->weight,
            'Neutered'       => $booking->neutered,
            'Complaint'      => $booking->complaint,
            'How Heard'      => $booking->how_heard,
            'Referred By'    => $booking->referred_by,
            'Visit Reason'   => $booking->reason,
            'Preferred Day'  => $booking->preferred_day,
            'Preferred Time' => $booking->preferred_time,
        ];
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>WhatsApp Booking</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-whatsapp-bookings.index') }}">WhatsApp Bookings</a></li>
                            <li class="breadcrumb-item active">#{{ $booking->reference() }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid enq-page">
            <div class="enq-header">
                <div class="enq-avatar"><i class="fab fa-whatsapp"></i></div>
                <div class="enq-header-meta">
                    <h1>{{ $booking->pet_name ?: 'Booking Request' }}</h1>
                    <p class="enq-header-sub">
                        <span>{{ $booking->parent_name ?: 'WhatsApp User' }}</span>
                        <span class="enq-sub-dot">·</span>
                        <span>{{ optional($booking->created_at)->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
                <div class="enq-header-ref">#{{ $booking->reference() }}</div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="enq-card">
                        <div class="enq-card-title">Booking Details</div>
                        <div class="enq-field-grid">
                            @foreach($fields as $label => $value)
                                <div class="enq-field">
                                    <span class="enq-label">{{ $label }}</span>
                                    <div class="enq-value">{{ $value !== null && $value !== '' ? $value : '—' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="https://wa.me/{{ $waDigits }}" target="_blank" class="btn btn-success">Reply on WhatsApp</a>
                            @if($booking->mobile)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $booking->mobile) }}" class="btn btn-outline-secondary">Call Sender</a>
                            @endif
                            @if($booking->email)
                                <a href="mailto:{{ $booking->email }}" class="btn btn-outline-secondary">Email Sender</a>
                            @endif
                            <a href="{{ route('manage-whatsapp-bookings.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Reference</span><span class="v">#{{ $booking->reference() }}</span></li>
                            <li><span class="k">WhatsApp</span><span class="v enq-mono">{{ $booking->wa_id }}</span></li>
                            <li><span class="k">Status</span><span class="v text-capitalize">{{ $booking->status }}</span></li>
                            <li><span class="k">Received</span><span class="v">{{ optional($booking->created_at)->format('d M Y, h:i A') }}</span></li>
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
