<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php $waDigits = preg_replace('/\D+/', '', $report->mobile ?: $report->wa_id); @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Medical Report Request</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-medical-reports.index') }}">Medical Report Requests</a></li>
                            <li class="breadcrumb-item active">#{{ $report->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid enq-page">
            <div class="enq-header">
                <div class="enq-avatar"><i class="fa fa-file-medical"></i></div>
                <div class="enq-header-meta">
                    <h1>{{ $report->client_name ?: 'WhatsApp User' }}</h1>
                    <p class="enq-header-sub">
                        <span>Medical report request</span>
                        <span class="enq-sub-dot">·</span>
                        <span>{{ optional($report->created_at)->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
                <div class="enq-header-ref">#{{ $report->reference() }}</div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="enq-card">
                        <div class="enq-card-title">Request Details</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Client Name</span><span class="v">{{ $report->client_name ?: '—' }}</span></li>
                            <li><span class="k">Patient Name</span><span class="v">{{ $report->patient_name ?: '—' }}</span></li>
                            <li><span class="k">Patient UID</span><span class="v">{{ $report->patient_uid ?: '—' }}</span></li>
                            <li><span class="k">Report Requested</span><span class="v">{{ $report->report_name ?: '—' }}</span></li>
                            @foreach((array) $report->extra as $label => $val)
                                <li><span class="k">{{ $label }}</span><span class="v">{{ $val }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="https://wa.me/{{ $waDigits }}" target="_blank" class="btn btn-success">Reply on WhatsApp</a>
                            <a href="tel:{{ $waDigits }}" class="btn btn-outline-secondary">Call Client</a>
                            <a href="{{ route('manage-medical-reports.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Mobile</span><span class="v enq-mono">{{ $report->mobile ?: '—' }}</span></li>
                            <li><span class="k">WhatsApp</span><span class="v enq-mono">{{ $report->wa_id }}</span></li>
                            <li><span class="k">Status</span><span class="v text-capitalize">{{ $report->status }}</span></li>
                            <li><span class="k">Received</span><span class="v">{{ optional($report->created_at)->format('d M Y, h:i A') }}</span></li>
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
