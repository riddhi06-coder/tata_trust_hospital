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
        <div class="page-title"><div class="row"><div class="col-12"><h3>Manage Permissions — {{ $role->name }}</h3></div></div></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.permissions.update', $role) }}" method="POST" class="theme-form">
                            @csrf @method('PUT')

                            <p class="text-muted">Tick the modules/actions this role is allowed to perform.</p>

                            <div class="row">
                                @foreach($permissions as $module => $perms)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border">
                                            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                                <strong>{{ $module }}</strong>
                                                <label class="form-check-label small">
                                                    <input type="checkbox" class="form-check-input module-toggle me-1" data-module="{{ $module }}">
                                                    Select all
                                                </label>
                                            </div>
                                            <div class="card-body">
                                                @foreach($perms as $perm)
                                                    <div class="form-check">
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $perm->id }}"
                                                            id="perm-{{ $perm->id }}"
                                                            class="form-check-input perm-checkbox"
                                                            data-module="{{ $module }}"
                                                            {{ in_array($perm->id, $assigned) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm-{{ $perm->id }}">
                                                            {{ $perm->name }}
                                                            <small class="text-muted">({{ $perm->slug }})</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('components.backend.footer')
</div>

@include('components.backend.main-js')

<script>
    document.querySelectorAll('.module-toggle').forEach(function (master) {
        master.addEventListener('change', function () {
            var module = master.dataset.module;
            document.querySelectorAll('.perm-checkbox[data-module="' + module + '"]').forEach(function (cb) {
                cb.checked = master.checked;
            });
        });
    });
</script>
</body>
</html>
