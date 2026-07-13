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
                    <div class="col-6"><h4>Communication #{{ $log->id }}</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.communication-logs.index') }}">Communication Logs</a></li>
                            <li class="breadcrumb-item active">#{{ $log->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $log->typeLabel() }}</h5>
                            <div class="d-flex gap-2">
                                <span class="badge {{ $log->channelBadgeClass() }} text-uppercase">{{ $log->channel }}</span>
                                <span class="badge {{ $log->statusBadgeClass() }} text-uppercase">{{ $log->status }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Recipient</small>{{ $log->recipient }}</div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Recipient Name</small>{{ $log->recipient_name ?: '—' }}</div>
                                @if($log->subject)
                                    <div class="col-12 mb-3"><small class="text-muted d-block">Subject</small>{{ $log->subject }}</div>
                                @endif
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">Message</small>
                                    <div class="status-note">{{ $log->message ?: '—' }}</div>
                                </div>
                                @if($log->status === 'failed' && $log->error)
                                    <div class="col-12 mb-3">
                                        <small class="text-muted d-block">Failure Reason</small>
                                        <div class="status-note" style="border-color:#fca5a5;color:#991b1b !important;background:#fef2f2 !important;">{{ $log->error }}</div>
                                    </div>
                                @endif
                                @if($log->provider_response)
                                    <div class="col-12 mb-3">
                                        <small class="text-muted d-block">Gateway / SMTP Response</small>
                                        <div class="status-note" style="font-family:monospace;">{{ $log->provider_response }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Metadata</h5></div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Sent At</span><span>{{ optional($log->created_at)->format('d M Y, h:i A') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Channel</span><span class="text-uppercase">{{ $log->channel }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Status</span><span class="text-uppercase">{{ $log->status }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Triggered By</span><span>{{ $log->triggered_by_name ?: 'System / Website' }}</span></li>
                            @if($log->appointmentUser)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Client</span>
                                    <a href="{{ route('manage-appointment-users.show', $log->appointmentUser->id) }}">{{ $log->appointmentUser->name ?: $log->appointmentUser->mobile }}</a>
                                </li>
                            @endif
                            @if($log->related_type && $log->related_id)
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Source</span><span>{{ class_basename($log->related_type) }} #{{ $log->related_id }}</span></li>
                            @endif
                        </ul>
                        <div class="card-body">
                            <a href="{{ route('admin.communication-logs.index') }}" class="btn btn-outline-secondary w-100">Back to List</a>
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
