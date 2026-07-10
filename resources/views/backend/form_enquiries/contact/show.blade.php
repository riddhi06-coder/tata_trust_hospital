<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Contact Enquiry Details</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-contact-enquiries.index') }}">Contact Enquiries</a></li>
                            <li class="breadcrumb-item active">#{{ $enquiry->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Enquiry #{{ $enquiry->id }}</h4>
                                <span class="meta-badge">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">Full Name</div>
                                        <div class="enquiry-detail-value">{{ $enquiry->full_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">Email</div>
                                        <div class="enquiry-detail-value"><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">Phone</div>
                                        <div class="enquiry-detail-value"><a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->phone) }}">{{ $enquiry->phone }}</a></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">Subject</div>
                                        <div class="enquiry-detail-value">{{ $enquiry->subject }}</div>
                                    </div>
                                </div>
                                @if($enquiry->message)
                                    <div class="col-md-12">
                                        <div class="enquiry-detail-row">
                                            <div class="enquiry-detail-label">Message</div>
                                            <div class="enquiry-detail-value enquiry-message">{{ $enquiry->message }}</div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">Submitted At</div>
                                        <div class="enquiry-detail-value">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="enquiry-detail-row">
                                        <div class="enquiry-detail-label">IP Address</div>
                                        <div class="enquiry-detail-value">{{ $enquiry->ip_address ?? '—' }}</div>
                                    </div>
                                </div>
                                @if($enquiry->user_agent)
                                    <div class="col-md-12">
                                        <div class="enquiry-detail-row">
                                            <div class="enquiry-detail-label">User Agent</div>
                                            <div class="enquiry-detail-value" style="font-size:0.85rem; color:#6b7280;">{{ $enquiry->user_agent }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 d-flex gap-2 justify-content-end">
                                <a href="{{ route('manage-contact-enquiries.index') }}" class="btn btn-secondary">Back to List</a>
                                <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ urlencode($enquiry->subject) }}" class="btn btn-primary">Reply via Email</a>
                            </div>

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
