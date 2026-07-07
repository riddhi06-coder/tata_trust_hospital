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
                    <div class="col-6"><h4>Add Job Posting</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-job-role.index') }}">Job Postings</a></li>
                            <li class="breadcrumb-item active">Add Job Posting</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Job Posting Form</h4>
                            <p class="f-m-light mt-1">Fill in the details and attach the Job Description.</p>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="row g-3 custom-input" action="{{ route('manage-job-role.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-6">
                                    <label class="form-label" for="job_position">Job Position <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="job_position" type="text" name="job_position"
                                        value="{{ old('job_position') }}" placeholder="e.g. Hospital Operations Manager">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="job_location">Job Location</label>
                                    <input class="form-control" id="job_location" type="text" name="job_location"
                                        value="{{ old('job_location') }}" placeholder="e.g. Mumbai, Maharashtra">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="job_type">Job Type <span class="txt-danger">*</span></label>
                                    <select class="form-select" id="job_type" name="job_type">
                                        <option value="">-- Select --</option>
                                        @foreach($jobTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('job_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="work_mode">Work Mode <span class="txt-danger">*</span></label>
                                    <select class="form-select" id="work_mode" name="work_mode">
                                        <option value="">-- Select --</option>
                                        @foreach($workModes as $key => $label)
                                            <option value="{{ $key }}" {{ old('work_mode') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="jd_file">Job Description (JD) <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="jd_file" type="file" name="jd_file"
                                        accept=".pdf,.doc,.docx">
                                    <small class="text-muted">PDF or Word document (.pdf, .doc, .docx) — max 5MB.</small>
                                    <div id="jd_file-name" class="mt-2 text-muted" style="display:none;"></div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-job-role.index') }}" class="btn btn-danger px-4">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('jd_file');
            var out = document.getElementById('jd_file-name');
            if (!input || !out) return;
            input.addEventListener('change', function () {
                var f = input.files && input.files[0];
                if (!f) { out.style.display = 'none'; out.textContent = ''; return; }
                var kb = f.size / 1024;
                var sizeLabel = kb > 1024 ? (kb / 1024).toFixed(2) + ' MB' : Math.round(kb) + ' KB';
                out.textContent = 'Selected: ' + f.name + ' (' + sizeLabel + ')';
                out.style.display = 'block';
            });
        });
    </script>

</body>

</html>
