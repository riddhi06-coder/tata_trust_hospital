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
                    <div class="col-6"><h4>Activity Log</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Activity Log</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Filters --}}
                            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mb-4 p-3 rounded" style="background:#f7f8fc; border:1px solid #eef0f6;">
                                <div class="row g-3 align-items-end">
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label mb-1">Search</label>
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Description, user or record ID">
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label mb-1">User</label>
                                        <select name="user_id" class="form-control">
                                            <option value="">All Users</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label mb-1">Module</label>
                                        <select name="module" class="form-control">
                                            <option value="">All Modules</option>
                                            @foreach($modules as $m)
                                                <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ $m }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label mb-1">Event</label>
                                        <select name="event" class="form-control">
                                            <option value="">All Events</option>
                                            @foreach($events as $e)
                                                <option value="{{ $e }}" {{ request('event') === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-lg-8 col-md-12">
                                        <label class="form-label mb-1">Date Range</label>
                                        <div class="d-flex gap-2">
                                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="min-width:0;" title="From date">
                                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="min-width:0;" title="To date">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary px-4">Filter</button>
                                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="min-width:150px;">Date &amp; Time</th>
                                            <th>User</th>
                                            <th>Event</th>
                                            <th>Module</th>
                                            <th>Description</th>
                                            <th class="text-end">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr>
                                                <td>{{ optional($log->created_at)->format('d M Y, h:i A') }}</td>
                                                <td>{{ $log->user_name ?? '—' }}</td>
                                                <td><span class="badge {{ $log->eventBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</span></td>
                                                <td>{{ $log->module ?? '—' }}</td>
                                                <td>{{ $log->description ?? '—' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-4">No activity found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $logs->links() }}
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
