<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
    @include('components.backend.appointment-styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4 class="mb-1">Dashboard</h4>
                        <p class="mb-0 text-muted">{{ $today->format('l, d M Y') }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('manage-appointments.index') }}" class="btn btn-primary btn-sm">View All Appointments</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- ===== Stat cards ===== --}}
            <div class="row g-3 mb-1">
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--primary">
                        <div>
                            <div class="dash-stat__num">{{ $counts['today'] }}</div>
                            <div class="dash-stat__label">Today's Appointments</div>
                        </div>
                        <div class="dash-stat__icon"><i class="fa fa-calendar-check-o"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--info">
                        <div>
                            <div class="dash-stat__num">{{ $counts['next2'] }}</div>
                            <div class="dash-stat__label">Next 2 Days</div>
                        </div>
                        <div class="dash-stat__icon"><i class="fa fa-calendar-o"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--warning">
                        <div>
                            <div class="dash-stat__num">{{ $counts['pending'] }}</div>
                            <div class="dash-stat__label">Pending Appointments</div>
                        </div>
                        <div class="dash-stat__icon"><i class="fa fa-clock-o"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--success">
                        <div>
                            <div class="dash-stat__num">{{ $counts['clients'] }}</div>
                            <div class="dash-stat__label">Total Clients</div>
                        </div>
                        <div class="dash-stat__icon"><i class="fa fa-users"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                {{-- ===== Today's appointments ===== --}}
                <div class="col-xl-8">
                    <div class="card h-100">
                        <div class="card-header dash-highlight-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Today's Appointments</h5>
                            <span class="dash-chip">{{ $today->format('d M Y') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-hover dash-table">
                                    <thead>
                                        <tr>
                                            <th>Owner</th>
                                            <th>Mobile</th>
                                            <th>Pet</th>
                                            <th>Consultation</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysAppointments as $a)
                                            <tr>
                                                <td>{{ $a->owner_name }}</td>
                                                <td>+91 {{ $a->mobile }}</td>
                                                <td>{{ $a->pet_name }} <span class="text-muted">({{ ucfirst($a->pet_type) }})</span></td>
                                                <td>{{ $a->consult_type === 'first' ? 'First-time' : 'Follow-up' }}</td>
                                                <td>
                                                    @if($a->status)
                                                        <span class="status-badge" style="background:{{ $a->status->color }};">{{ $a->status->name }}</span>
                                                    @else
                                                        <span class="status-badge status-badge--muted">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('manage-appointments.show', $a->id) }}" class="btn btn-sm btn-primary py-1 px-2">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="dash-empty">No appointments scheduled for today.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Status overview ===== --}}
                <div class="col-xl-4">
                    <div class="card h-100">
                        <div class="card-header"><h5 class="mb-0">Appointment Status Overview</h5></div>
                        <div class="card-body">
                            @if($counts['total'] > 0)
                                <div id="statusChart"></div>
                            @endif
                            <ul class="list-unstyled mb-0 mt-2">
                                @foreach($statusBreakdown as $s)
                                    <li class="dash-list-item">
                                        <span><span class="dash-status-dot" style="background:{{ $s->color }};"></span>{{ $s->name }}</span>
                                        <span class="badge bg-light text-dark">{{ $s->appointments_count }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Next 2 days ===== --}}
            @php
                $days = [
                    ['label' => 'Tomorrow',  'date' => $tomorrow, 'items' => $tomorrowAppts],
                    ['label' => 'Day After', 'date' => $dayAfter, 'items' => $dayAfterAppts],
                ];
            @endphp
            <div class="row g-3 mt-1">
                @foreach($days as $d)
                    <div class="col-md-6">
                        <div class="card dash-day-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <span class="dash-day-pill">{{ $d['label'] }}</span>
                                    <span class="dash-day-date">{{ $d['date']->format('D, d M Y') }}</span>
                                </span>
                                <span class="dash-chip">{{ $d['items']->count() }}</span>
                            </div>
                            <div class="card-body">
                                @forelse($d['items'] as $a)
                                    <div class="dash-list-item">
                                        <div>
                                            <div class="dash-list-item__title">{{ $a->owner_name }}</div>
                                            <div class="dash-list-item__sub">{{ $a->pet_name }} ({{ ucfirst($a->pet_type) }}) · +91 {{ $a->mobile }}</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($a->status)
                                                <span class="status-badge" style="background:{{ $a->status->color }};">{{ $a->status->name }}</span>
                                            @else
                                                <span class="status-badge status-badge--muted">Pending</span>
                                            @endif
                                            <a href="{{ route('manage-appointments.show', $a->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">View</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="dash-empty">No appointments scheduled.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===== Recent form submissions ===== --}}
            <div class="row g-3 mt-1 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Contact Enquiries</h5>
                            <a href="{{ route('manage-contact-enquiries.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2">View All</a>
                        </div>
                        <div class="card-body">
                            @forelse($recentContacts as $c)
                                <div class="dash-list-item">
                                    <div>
                                        <div class="dash-list-item__title">{{ $c->full_name }}</div>
                                        <div class="dash-list-item__sub">{{ Str::limit($c->subject, 40) }}</div>
                                    </div>
                                    <span class="dash-list-item__sub">{{ optional($c->created_at)->format('d M Y') }}</span>
                                </div>
                            @empty
                                <div class="dash-empty">No contact enquiries yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Job Applications</h5>
                            <a href="{{ route('manage-job-applications.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2">View All</a>
                        </div>
                        <div class="card-body">
                            @forelse($recentJobs as $j)
                                <div class="dash-list-item">
                                    <div>
                                        <div class="dash-list-item__title">{{ $j->full_name }}</div>
                                        <div class="dash-list-item__sub">{{ Str::limit($j->applying_for, 40) }}</div>
                                    </div>
                                    <span class="dash-list-item__sub">{{ optional($j->created_at)->format('d M Y') }}</span>
                                </div>
                            @empty
                                <div class="dash-empty">No job applications yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    @if($counts['total'] > 0)
    <script>
        (function () {
            var el = document.querySelector('#statusChart');
            if (!el || typeof ApexCharts === 'undefined') return;

            new ApexCharts(el, {
                chart:   { type: 'donut', height: 240 },
                labels:  @json($statusBreakdown->pluck('name')),
                series:  @json($statusBreakdown->pluck('appointments_count')->map(fn ($n) => (int) $n)),
                colors:  @json($statusBreakdown->pluck('color')),
                legend:  { show: false },
                dataLabels: { enabled: false },
                stroke:  { width: 2 },
                tooltip: { y: { formatter: function (v) { return v + ' appointment(s)'; } } }
            }).render();
        })();
    </script>
    @endif
</body>
</html>
