<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $initials = collect(explode(' ', trim($enquiry->full_name)))
            ->filter()
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Contact Enquiry</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-contact-enquiries.index') }}">Contact Enquiries</a></li>
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
                    <h1>{{ $enquiry->full_name }}</h1>
                    <p class="enq-header-sub">
                        <span>{{ $enquiry->subject }}</span>
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
                        <div class="enq-card-title">Sender Information</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Full Name</span>
                                <div class="enq-value">{{ $enquiry->full_name }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Email Address</span>
                                <div class="enq-value"><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Phone Number</span>
                                <div class="enq-value"><a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->phone) }}">{{ $enquiry->phone }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Submitted</span>
                                <div class="enq-value">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Subject</div>
                        <div class="enq-field">
                            <div class="enq-value">{{ $enquiry->subject }}</div>
                        </div>
                    </div>

                    @if($enquiry->message)
                        <div class="enq-card">
                            <div class="enq-card-title">Message</div>
                            <p class="enq-message-body">{{ $enquiry->message }}</p>
                        </div>
                    @endif

                </div>

                {{-- RIGHT: Actions + metadata --}}
                <div class="col-lg-4">

                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ urlencode($enquiry->subject) }}" class="btn btn-primary">Reply via Email</a>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->phone) }}" class="btn btn-outline-secondary">Call Sender</a>
                            <a href="{{ route('manage-contact-enquiries.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Submission Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Enquiry ID</span><span class="v">#{{ str_pad($enquiry->id, 4, '0', STR_PAD_LEFT) }}</span></li>
                            <li><span class="k">Submitted</span><span class="v">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</span></li>
                            @if($enquiry->ip_address)
                                <li><span class="k">IP Address</span><span class="v enq-mono">{{ $enquiry->ip_address }}</span></li>
                            @endif
                            @if($enquiry->user_agent)
                                <li>
                                    <span class="k">User Agent</span>
                                    <span class="v enq-mono" title="{{ $enquiry->user_agent }}">{{ \Illuminate\Support\Str::limit($enquiry->user_agent, 32) }}</span>
                                </li>
                            @endif
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
