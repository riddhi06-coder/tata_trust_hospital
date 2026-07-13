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
                    <div class="col-6"><h4>Communication Logs</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Communication Logs</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            {{-- Summary tiles --}}
            <div class="row g-3 mb-1">
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--primary">
                        <div><div class="dash-stat__num">{{ $summary['total'] }}</div><div class="dash-stat__label">Total Messages</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-paper-plane"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--info">
                        <div><div class="dash-stat__num">{{ $summary['sms'] }}</div><div class="dash-stat__label">SMS</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-mobile"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--success">
                        <div><div class="dash-stat__num">{{ $summary['email'] }}</div><div class="dash-stat__label">Emails</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-envelope"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--warning">
                        <div><div class="dash-stat__num">{{ $summary['failed'] }}</div><div class="dash-stat__label">Failed</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Filters --}}
                            <form method="GET" action="{{ route('admin.communication-logs.index') }}" class="mb-4">
                                <div class="appt-filter-panel">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Channel</label>
                                            <select name="channel" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                                                <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Type</label>
                                            <select name="type" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                @foreach($types as $t)
                                                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $t)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Status</label>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">From</label>
                                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">To</label>
                                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Recipient / Subject</label>
                                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Email, mobile, name…">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm px-3">Apply</button>
                                        <a href="{{ route('admin.communication-logs.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>When</th>
                                            <th>Channel</th>
                                            <th>Type</th>
                                            <th>Recipient</th>
                                            <th>Status</th>
                                            <th>Triggered By</th>
                                            <th class="text-end">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr>
                                                <td class="text-nowrap">{{ optional($log->created_at)->format('d M Y, h:i A') }}</td>
                                                <td><span class="badge {{ $log->channelBadgeClass() }} text-uppercase">{{ $log->channel }}</span></td>
                                                <td>{{ $log->typeLabel() }}</td>
                                                <td>
                                                    <div>{{ $log->recipient }}</div>
                                                    @if($log->recipient_name)<div class="text-muted small">{{ $log->recipient_name }}</div>@endif
                                                </td>
                                                <td><span class="badge {{ $log->statusBadgeClass() }} text-uppercase">{{ $log->status }}</span></td>
                                                <td>{{ $log->triggered_by_name ?: 'System / Website' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.communication-logs.show', $log->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">No communication records found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">{{ $logs->links() }}</div>

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
