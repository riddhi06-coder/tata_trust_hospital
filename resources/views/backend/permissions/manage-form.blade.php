<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
@include('components.backend.header')
@include('components.backend.sidebar')

@php
    $isEdit = isset($permission) && $permission;
    $action = $isEdit
        ? route('admin.permissions.manage.update', $permission)
        : route('admin.permissions.manage.store');
@endphp

<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row"><div class="col-12">
                <h3>{{ $isEdit ? 'Edit Permission — '.$permission->name : 'New Permission' }}</h3>
            </div></div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ $action }}" method="POST" class="theme-form">
                            @csrf
                            @if($isEdit) @method('PUT') @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $permission->name ?? '') }}" required
                                           placeholder="Short action verb, e.g. View, Create, Edit, Delete">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Module <span class="text-danger">*</span></label>
                                    <input type="text" name="module" class="form-control @error('module') is-invalid @enderror"
                                           value="{{ old('module', $permission->module ?? '') }}" required
                                           placeholder="e.g. Products (the sidebar tab this belongs to)"
                                           list="module-list">
                                    <datalist id="module-list">
                                        @foreach($modules as $m)
                                            <option value="{{ $m }}"></option>
                                        @endforeach
                                    </datalist>
                                    @error('module')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Groups permissions on the matrix screen. Reuse existing values or type a new one.</small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control"
                                           value="{{ old('description', $permission->description ?? '') }}"
                                           placeholder="What this permission allows the role to do">
                                </div>

                                @if($isEdit)
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Slug</label>
                                        <div><code class="fs-6">{{ $permission->slug }}</code></div>
                                        <small class="text-muted">Slug is preserved on edit so route middleware references keep working. Tip: it's used as <code>->middleware('permission:{{ $permission->slug }}')</code> on routes.</small>
                                    </div>
                                @else
                                    <div class="col-12 mb-3">
                                        <small class="text-muted">A slug like <code>module.name</code> will be generated automatically (e.g. Module "Products" + Name "View" → <code>products.view</code>).</small>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Save Changes' : 'Create Permission' }}</button>
                            <a href="{{ route('admin.permissions.manage') }}" class="btn btn-light">Cancel</a>
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
</body>
</html>
