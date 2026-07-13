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
                    <div class="col-6"><h4>Appointments Report</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item active">Appointments</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <form id="reportFilterForm" class="mb-3"
                  data-filter-url="{{ route('admin.reports.appointments.filter') }}"
                  data-export-url="{{ route('admin.reports.appointments.export') }}">
                @csrf
                <div class="appt-filter-panel">
                    <div class="row g-2 align-items-end">
                        @include('backend.reports.partials._period_fields')
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label small fw-semibold mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm js-auto-filter">
                                <option value="">All</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s->id }}" {{ (string) $filters['status'] === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label small fw-semibold mb-1">Pet Type</label>
                            <select name="pet_type" class="form-select form-select-sm js-auto-filter">
                                <option value="">All</option>
                                <option value="dog" {{ $filters['pet_type'] === 'dog' ? 'selected' : '' }}>Dog</option>
                                <option value="cat" {{ $filters['pet_type'] === 'cat' ? 'selected' : '' }}>Cat</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label small fw-semibold mb-1">Consultation</label>
                            <select name="consult_type" class="form-select form-select-sm js-auto-filter">
                                <option value="">All</option>
                                <option value="first" {{ $filters['consult_type'] === 'first' ? 'selected' : '' }}>First-time</option>
                                <option value="followup" {{ $filters['consult_type'] === 'followup' ? 'selected' : '' }}>Follow-up</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" id="reportReset" class="btn btn-outline-secondary btn-sm px-3">Reset</button>
                        <a href="{{ route('admin.reports.appointments.export') }}" id="reportExport" class="btn btn-success btn-sm px-3"><i class="fa fa-download"></i> Download CSV</a>
                    </div>
                </div>
            </form>

            <div id="reportBodyWrap" class="position-relative">
                <div id="reportLoader" class="appt-loader d-none"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div></div>
                <div id="reportBody">
                    @include('backend.reports.partials.appointments_body')
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
    @include('components.backend.report-js')
</body>
</html>
