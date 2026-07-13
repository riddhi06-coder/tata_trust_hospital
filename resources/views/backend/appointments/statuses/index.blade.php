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
                    <div class="col-6"><h4>Manage Statuses</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">Appointments</li>
                            <li class="breadcrumb-item active">Manage Statuses</li>
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
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item">Appointments</li>
                                        <li class="breadcrumb-item active">Statuses</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('manage-appointment-statuses.create') }}" class="btn btn-primary px-5 radius-30">+ Add Status</a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Status</th>
                                            <th>Order</th>
                                            <th>Default</th>
                                            <th>Active</th>
                                            <th>In Use</th>
                                            <th class="text-end appt-actions-col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($statuses as $i => $status)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td><span class="status-badge" style="background:{{ $status->color }};">{{ $status->name }}</span></td>
                                                <td>{{ $status->sort_order }}</td>
                                                <td>{!! $status->is_default ? '<span class="badge bg-success">Default</span>' : '<span class="text-muted">—</span>' !!}</td>
                                                <td>{!! $status->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                                                <td><span class="badge bg-info">{{ $status->appointments_count }}</span></td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-appointment-statuses.edit', $status->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-appointment-statuses.destroy', $status->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this status?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">No statuses added yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
