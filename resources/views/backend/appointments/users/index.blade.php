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
                    <div class="col-6"><h4>Appointment Users</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">Appointments</li>
                            <li class="breadcrumb-item active">Appointment Users</li>
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
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item">Appointments</li>
                                        <li class="breadcrumb-item active">Appointment Users</li>
                                    </ol>
                                </nav>
                                <span class="badge bg-primary">{{ $users->total() }} Total Clients</span>
                            </div>

                            {{-- Search --}}
                            <form method="GET" action="{{ route('manage-appointment-users.index') }}" class="row g-2 mb-3 justify-content-end">
                                <div class="col-md-4 col-sm-8">
                                    <input type="text" name="search" value="{{ $search }}" class="form-control"
                                           placeholder="Search by name, mobile or email…">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    @if($search !== '')
                                        <a href="{{ route('manage-appointment-users.index') }}" class="btn btn-outline-secondary">Reset</a>
                                    @endif
                                </div>
                            </form>

                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Appointments</th>
                                            <th>Last Verified</th>
                                            <th class="text-end" style="min-width:120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $i => $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $i }}</td>
                                                <td>{{ $user->name ?: '—' }}</td>
                                                <td>+91 {{ $user->mobile }}</td>
                                                <td>{{ $user->email ?: '—' }}</td>
                                                <td><span class="badge bg-info">{{ $user->appointments_count }}</span></td>
                                                <td>{{ optional($user->last_verified_at)->format('d M Y, h:i A') ?: '—' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('manage-appointment-users.show', $user->id) }}" class="btn btn-sm btn-primary">History</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">No appointment users found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $users->links() }}
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
