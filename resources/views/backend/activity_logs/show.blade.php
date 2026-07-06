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
                    <div class="col-6"><h4>Activity Detail</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">Activity Log</a></li>
                            <li class="breadcrumb-item active">Detail #{{ $log->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <span class="badge {{ $log->eventBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</span>
                                {{ $log->description }}
                            </h4>
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary px-4">Back</a>
                        </div>
                        <div class="card-body">

                            <div class="row g-3 mb-4">
                                <div class="col-md-4"><small class="text-muted d-block">User</small><strong>{{ $log->user_name ?? '—' }}</strong></div>
                                <div class="col-md-4"><small class="text-muted d-block">Date &amp; Time</small><strong>{{ optional($log->created_at)->format('d M Y, h:i:s A') }}</strong></div>
                                <div class="col-md-4"><small class="text-muted d-block">Module</small><strong>{{ $log->module ?? '—' }}</strong></div>

                                <div class="col-md-4"><small class="text-muted d-block">Record</small><strong>{{ $log->auditable_id ? '#'.$log->auditable_id : '—' }}</strong></div>
                                <div class="col-md-4"><small class="text-muted d-block">IP Address</small><strong>{{ $log->ip_address ?? '—' }}</strong></div>
                                <div class="col-md-4"><small class="text-muted d-block">Method</small><strong>{{ $log->method ?? '—' }}</strong></div>

                                <div class="col-md-12"><small class="text-muted d-block">URL</small><span class="text-break">{{ $log->url ?? '—' }}</span></div>
                                <div class="col-md-12"><small class="text-muted d-block">User Agent</small><span class="text-break">{{ $log->user_agent ?? '—' }}</span></div>
                            </div>

                            @php
                                use Illuminate\Support\Str;

                                // Technical noise a non-technical admin doesn't need to see.
                                $hidden = ['id', 'remember_token', 'email_verified_at'];
                                $fields = $log->changed_fields ?? array_keys(($log->new_values ?? []) + ($log->old_values ?? []));
                                $fields = array_values(array_filter($fields, fn ($f) => ! in_array($f, $hidden, true)));

                                // Turn raw stored values into something a layman can read.
                                $friendly = function ($field, $v) use ($users) {
                                    if (is_null($v) || $v === '') return '—';

                                    // "…_by" columns hold a user id → show the person's name.
                                    if (Str::endsWith($field, '_by')) {
                                        return $users[$v] ?? ('User #'.$v);
                                    }

                                    // Yes/No flags stored as 0/1.
                                    $boolish = in_array($field, ['is_active', 'is_protected', 'show_on_home', 'show_on_team_page'], true)
                                        || Str::startsWith($field, ['is_', 'show_']);
                                    if ($boolish && in_array((string) $v, ['0', '1'], true)) {
                                        return $v ? 'Yes' : 'No';
                                    }

                                    // Date/time columns.
                                    if (Str::endsWith($field, '_at') && ! is_array($v)) {
                                        try { return \Carbon\Carbon::parse($v)->format('d M Y, h:i A'); } catch (\Throwable $e) {}
                                    }

                                    if (is_bool($v)) return $v ? 'Yes' : 'No';
                                    if (is_array($v)) return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                    // Strip HTML from rich-text so it reads as plain words.
                                    $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) $v)));
                                    return $plain === '' ? '—' : $plain;
                                };
                            @endphp

                            @if(!empty($fields))
                                <h5 class="mb-2">Changes</h5>
                                <div class="table-responsive custom-scrollbar">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:22%;">Field</th>
                                                <th style="width:39%;">Old Value</th>
                                                <th style="width:39%;">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fields as $field)
                                                <tr>
                                                    <td><strong>{{ Str::headline($field) }}</strong></td>
                                                    <td><span class="text-danger text-break">{{ Str::limit($friendly($field, data_get($log->old_values, $field)), 400) }}</span></td>
                                                    <td><span class="text-success text-break">{{ Str::limit($friendly($field, data_get($log->new_values, $field)), 400) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No field-level changes were recorded for this entry.</p>
                            @endif

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
