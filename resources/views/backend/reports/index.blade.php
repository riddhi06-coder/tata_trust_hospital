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
                    <div class="col-6"><h4>Reports</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg></a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-3">
                @php
                    $cards = [
                        ['route' => 'admin.reports.appointments',  'title' => 'Appointments Report', 'desc' => 'Volume trends, status split, consultation & pet mix.', 'icon' => 'fa-calendar-check-o', 'variant' => 'primary'],
                        ['route' => 'admin.reports.operational',   'title' => 'Operational Report',  'desc' => 'Status changes, cancellations, reschedules, backlog.', 'icon' => 'fa-exchange', 'variant' => 'info'],
                        ['route' => 'admin.reports.clients',       'title' => 'Clients Report',      'desc' => 'New vs repeat clients and top clients by visits.',    'icon' => 'fa-users', 'variant' => 'success'],
                    ];
                    if ($isSuperAdmin) {
                        $cards[] = ['route' => 'admin.reports.communication', 'title' => 'Communication Report', 'desc' => 'Mail & SMS sent vs failed (Super Admin only).', 'icon' => 'fa-paper-plane', 'variant' => 'warning'];
                    }
                @endphp

                @foreach($cards as $c)
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route($c['route']) }}" class="text-decoration-none">
                            <div class="dash-stat dash-stat--{{ $c['variant'] }} h-100" style="align-items:flex-start;">
                                <div>
                                    <div class="dash-stat__num" style="font-size:18px;">{{ $c['title'] }}</div>
                                    <div class="dash-stat__label">{{ $c['desc'] }}</div>
                                </div>
                                <div class="dash-stat__icon"><i class="fa {{ $c['icon'] }}"></i></div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
