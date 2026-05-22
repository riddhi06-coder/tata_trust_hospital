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
                <div class="col-6"><h3>Permissions by Role</h3></div>
                <div class="col-6 text-end">
                    @if(auth()->user()->hasPermission('permissions.assign'))
                        <a href="{{ route('admin.permissions.manage') }}" class="btn btn-outline-secondary">Manage Permission Catalog</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-3">Choose a role to assign which sections/actions it can access. Super Admin always has full access.</p>
                        <div class="table-responsive">
                            <table id="basic-1" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-end" style="min-width:140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->is_protected)
                                                    <span class="badge bg-warning text-dark ms-1">protected</span>
                                                @endif
                                            </td>
                                            <td>{{ $role->description ?: '—' }}</td>
                                            <td>
                                                @if($role->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if(auth()->user()->hasPermission('permissions.assign') && ! $role->is_protected)
                                                    <a href="{{ route('admin.permissions.edit', $role) }}" class="btn btn-sm btn-primary">Manage</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
</div>

@include('components.backend.main-js')
</body>
</html>
