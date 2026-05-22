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
                <div class="col-6"><h3>Permission Catalog</h3></div>
                <div class="col-6 text-end">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Back to Roles</a>
                    @if(auth()->user()->hasPermission('permissions.assign'))
                        <a href="{{ route('admin.permissions.manage.create') }}" class="btn btn-primary">+ New Permission</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Add a permission here when you create a new tab/module. Anything in this catalog appears automatically
                            in the per-role permissions matrix. Slugs are generated automatically from the module + name.
                        </p>

                        @forelse($grouped as $module => $perms)
                            <h6 class="mt-3 mb-2">{{ $module }}</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Slug <small class="text-muted">(auto)</small></th>
                                            <th>Description</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($perms as $perm)
                                            <tr>
                                                <td>{{ $perm->name }}</td>
                                                <td><code>{{ $perm->slug }}</code></td>
                                                <td>{{ $perm->description ?: '—' }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        @if(auth()->user()->hasPermission('permissions.assign'))
                                                            <a href="{{ route('admin.permissions.manage.edit', $perm) }}" class="btn btn-sm btn-primary">Edit</a>
                                                            <form action="{{ route('admin.permissions.manage.destroy', $perm) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this permission? Any role that had it will lose access to anything gated by this slug.')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No permissions defined yet.</p>
                        @endforelse
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
