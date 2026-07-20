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
                    <div class="col-6"><h4>Edit Privacy Policy</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-privacy-policy.index') }}">Privacy Policy</a></li>
                            <li class="breadcrumb-item active">Edit Privacy Policy</li>
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
                            <h4>Update Privacy Policy</h4>
                            <p class="f-m-light mt-1">Replace the uploaded document below.</p>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-privacy-policy.update', $policy->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label" for="name">Policy Name <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name', $policy->name) }}" placeholder="e.g. Privacy Policy, Refund Policy, Terms &amp; Conditions">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="file">Policy Document</label>
                                    <input class="form-control" id="file" type="file" name="file" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Leave blank to keep current. PDF or Word (.pdf, .doc, .docx) — max 5MB.</small>

                                    @if($policy->file)
                                        <div class="mt-2">
                                            <a href="{{ asset('home/privacy/'.$policy->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                View current document
                                            </a>
                                            <span class="text-muted ms-2">{{ $policy->file }}</span>
                                        </div>
                                    @endif

                                    <div id="file-name" class="mt-2 text-muted" style="display:none;"></div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-privacy-policy.index') }}" class="btn btn-danger px-4">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Update</button>
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
            var input = document.getElementById('file');
            var out = document.getElementById('file-name');
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
