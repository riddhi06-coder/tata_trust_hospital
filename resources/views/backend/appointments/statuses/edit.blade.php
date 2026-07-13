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
                    <div class="col-6"><h4>Edit Status</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-appointment-statuses.index') }}">Statuses</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('manage-appointment-statuses.update', $status->id) }}">
                                @csrf @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $status->name) }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Badge Colour</label>
                                        <input type="color" name="color" value="{{ old('color', $status->color) }}" class="form-control form-control-color w-100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order', $status->sort_order) }}" class="form-control" min="0">
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $status->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Active (available in status dropdowns)</label>
                                </div>
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" {{ old('is_default', $status->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isDefault">Set as default (assigned to new appointments)</label>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Status</button>
                                <a href="{{ route('manage-appointment-statuses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </form>
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
