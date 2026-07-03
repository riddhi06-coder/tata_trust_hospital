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
                    <div class="col-6"><h4>Add Speciality</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-specialities.index') }}">Specialities</a></li>
                            <li class="breadcrumb-item active">Add Speciality</li>
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
                            <h4>Speciality Form</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — set the banner and service section details, then add your first speciality.</p>
                            @else
                                <p class="f-m-light mt-1">Fill in the details below.</p>
                            @endif
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

                            <form class="row g-3 custom-input" action="{{ route('manage-specialities.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                @if($showBanner)
                                    <div class="col-md-6">
                                        <label class="form-label" for="banner_heading">Banner Heading</label>
                                        <input class="form-control" id="banner_heading" type="text" name="banner_heading"
                                            value="{{ old('banner_heading', $settings->banner_heading ?? '') }}"
                                            placeholder="Enter Banner Heading">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="banner_image">Banner Image</label>
                                        <input class="form-control" id="banner_image" type="file" name="banner_image"
                                            accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">jpg, jpeg, png, webp — max 10MB</small>
                                        <div class="mt-2">
                                            <img id="banner_image-preview" src="" alt=""
                                                style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="service_section_heading">Service Section Heading</label>
                                        <input class="form-control" id="service_section_heading" type="text" name="service_section_heading"
                                            value="{{ old('service_section_heading', $settings->service_section_heading ?? '') }}"
                                            placeholder="Enter Service Section Heading">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="editor">Service Description</label>
                                        <textarea class="form-control" id="editor" name="service_description" rows="6"
                                            placeholder="Enter Service Description">{{ old('service_description', $settings->service_description ?? '') }}</textarea>
                                    </div>

                                    <div class="col-12"><hr></div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label" for="speciality">Speciality <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="speciality" type="text" name="speciality"
                                        value="{{ old('speciality') }}"
                                        placeholder="Enter Speciality Name">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="image">Speciality Image <span class="txt-danger">*</span></label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control" required>
                                    <small class="text-muted">jpg, jpeg, png, webp — max 5MB</small>
                                    <div class="mt-2">
                                        <img id="image-preview" src="" alt=""
                                            class="speciality-image-thumb"
                                            style="max-height:160px; max-width:240px; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-specialities.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            function wirePreview(inputId, previewId) {
                var input   = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        preview.src = '';
                        preview.style.display = 'none';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (ev) {
                        preview.src = ev.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            wirePreview('image',        'image-preview');
            wirePreview('banner_image', 'banner_image-preview');
        });
    </script>
</body>
</html>
