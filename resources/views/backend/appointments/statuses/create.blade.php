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
                    <div class="col-6"><h4>Add Status</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-appointment-statuses.index') }}">Statuses</a></li>
                            <li class="breadcrumb-item active">Add</li>
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
                            <form method="POST" action="{{ route('manage-appointment-statuses.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="e.g. In Progress">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Badge Colour</label>
                                        <input type="color" name="color" value="{{ old('color', '#0d6efd') }}" class="form-control form-control-color w-100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Active (available in status dropdowns)</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isDefault">Set as default (assigned to new appointments)</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="requires_appointment_date" value="1" id="requiresDate" {{ old('requires_appointment_date') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requiresDate">Require a new appointment date (e.g. Rescheduled)</label>
                                </div>
                                <div class="mb-4 col-md-5">
                                    <label class="form-label">Send SMS when this status is applied</label>
                                    <select name="sms_trigger" class="form-select">
                                        <option value="" {{ old('sms_trigger') === null || old('sms_trigger') === '' ? 'selected' : '' }}>No SMS</option>
                                        <option value="cancellation" {{ old('sms_trigger') === 'cancellation' ? 'selected' : '' }}>Cancellation SMS</option>
                                        <option value="reschedule" {{ old('sms_trigger') === 'reschedule' ? 'selected' : '' }}>Reschedule SMS</option>
                                    </select>
                                    <small class="text-muted">Reschedule SMS also forces a new appointment date to be entered.</small>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Status</button>
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
