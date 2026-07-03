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
                    <div class="col-6"><h4>Edit Speciality Details</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('speciality-details.index') }}">Speciality Details</a></li>
                            <li class="breadcrumb-item active">Edit Speciality Details</li>
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
                            <h4>Update Speciality Details</h4>
                            <p class="f-m-light mt-1">Existing images are kept unless you upload new ones.</p>
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

                            <form class="row g-4 custom-input" action="{{ route('speciality-details.update', $detail->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label" for="speciality_id">Speciality <span class="txt-danger">*</span></label>
                                    <select class="form-control" id="speciality_id" name="speciality_id" required>
                                        <option value="">— Select Speciality —</option>
                                        @foreach($specialities as $spec)
                                            <option value="{{ $spec->id }}" {{ (string) old('speciality_id', $detail->speciality_id) === (string) $spec->id ? 'selected' : '' }}>
                                                {{ $spec->speciality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="banner_image">Banner Image</label>
                                    <input class="form-control" id="banner_image" type="file" name="banner_image"
                                        accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="banner_image-preview"
                                            src="{{ asset('home/speciality-details/'.$detail->banner_image) }}"
                                            data-existing="{{ asset('home/speciality-details/'.$detail->banner_image) }}"
                                            alt=""
                                            style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="section_image">Section Image</label>
                                    <input class="form-control" id="section_image" type="file" name="section_image"
                                        accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="section_image-preview"
                                            src="{{ asset('home/speciality-details/'.$detail->section_image) }}"
                                            data-existing="{{ asset('home/speciality-details/'.$detail->section_image) }}"
                                            alt=""
                                            style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="section_heading">Section Heading <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="section_heading" type="text" name="section_heading"
                                        value="{{ old('section_heading', $detail->section_heading) }}"
                                        placeholder="Enter Section Heading" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="editor">Section Description <span class="txt-danger">*</span></label>
                                    <textarea class="form-control" id="editor" name="section_description" rows="6"
                                        placeholder="Enter Section Description">{{ old('section_description', $detail->section_description) }}</textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="service_heading">Service Heading <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="service_heading" type="text" name="service_heading"
                                        value="{{ old('service_heading', $detail->service_heading) }}"
                                        placeholder="Enter Service Heading" required>
                                </div>

                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Services <span class="txt-danger">*</span></label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-service">+ Add More</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-0" id="services-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:60px;">#</th>
                                                    <th>Description</th>
                                                    <th style="width:110px;" class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="services-body">
                                                @php
                                                    $servicesList = old('services', $detail->services ?? ['']);
                                                    if (!is_array($servicesList) || count($servicesList) === 0) {
                                                        $servicesList = [''];
                                                    }
                                                @endphp
                                                @foreach($servicesList as $idx => $svc)
                                                    <tr class="service-row">
                                                        <td class="row-index">{{ $idx + 1 }}</td>
                                                        <td>
                                                            <textarea class="form-control" name="services[]" rows="2"
                                                                placeholder="Enter service description" required>{{ $svc }}</textarea>
                                                        </td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-sm btn-danger remove-service">Remove</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="short_info">Short Info</label>
                                    <textarea class="form-control" id="short_info" name="short_info" rows="3"
                                        placeholder="Enter Short Info (optional)">{{ old('short_info', $detail->short_info) }}</textarea>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('speciality-details.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            function wirePreview(inputId, previewId) {
                var input   = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        preview.src = preview.getAttribute('data-existing') || '';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; };
                    reader.readAsDataURL(file);
                });
            }

            wirePreview('banner_image',  'banner_image-preview');
            wirePreview('section_image', 'section_image-preview');

            var body = document.getElementById('services-body');

            function renumberRows() {
                body.querySelectorAll('.service-row').forEach(function (row, i) {
                    row.querySelector('.row-index').textContent = i + 1;
                });
            }

            document.getElementById('add-service').addEventListener('click', function () {
                var count = body.querySelectorAll('.service-row').length + 1;
                var row = document.createElement('tr');
                row.className = 'service-row';
                row.innerHTML =
                    '<td class="row-index">' + count + '</td>' +
                    '<td><textarea class="form-control" name="services[]" rows="2" placeholder="Enter service description" required></textarea></td>' +
                    '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-service">Remove</button></td>';
                body.appendChild(row);
            });

            body.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-service')) return;
                var rows = body.querySelectorAll('.service-row');
                if (rows.length <= 1) {
                    alert('At least one service is required.');
                    return;
                }
                e.target.closest('.service-row').remove();
                renumberRows();
            });
        });
    </script>
</body>
</html>
