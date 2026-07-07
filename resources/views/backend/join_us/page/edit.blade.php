<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
    <style>
        .section-heading {
            background: linear-gradient(90deg, #eef2ff 0%, #f8fafc 100%);
            padding: 10px 16px;
            margin: -1rem -1rem 1rem -1rem;
            border-bottom: 2px solid #6366f1;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        .section-heading h5,
        .section-heading h6 {
            margin: 0 !important;
            font-weight: 700;
            color: #1e3a8a;
            font-size: 1.05rem;
        }
        .section-subheading {
            font-weight: 600;
            color: #334155;
            padding-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
        }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $bannerUrl = $joinPage->banner_image ? asset('home/join-us/banner/'.$joinPage->banner_image) : '';
        $extraUrl  = $joinPage->extra_background_image ? asset('home/join-us/extra/'.$joinPage->extra_background_image) : '';
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Join Us Page Details</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-join-page.index') }}">Join Us</a></li>
                            <li class="breadcrumb-item active">Edit Join Us Page</li>
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
                            <h4>Update Join Us Page</h4>
                            <p class="f-m-light mt-1">Update the sections below.</p>
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

                            <form class="row g-4 custom-input" action="{{ route('manage-join-page.update', $joinPage->id) }}" method="POST" enctype="multipart/form-data" id="join-us-form">
                                @csrf
                                @method('PUT')

                                {{-- ===================== BANNER ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Banner</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="banner_heading">Banner Heading <span class="txt-danger">*</span></label>
                                                <textarea class="form-control" id="banner_heading" name="banner_heading" rows="2"
                                                    placeholder="Enter Banner Heading (press Enter to break line)">{{ old('banner_heading', $joinPage->banner_heading) }}</textarea>
                                                <small class="text-muted">Line breaks appear as new lines on the site (e.g. type "Be A Part Of" &crarr; "Our Team!").</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="banner_image">Banner Image</label>
                                                <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
                                                <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                                <div class="mt-2">
                                                    <img id="banner_image-preview"
                                                        src="{{ $bannerUrl }}"
                                                        data-existing="{{ $bannerUrl }}"
                                                        alt=""
                                                        style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $bannerUrl ? '' : 'display:none;' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== SECTION ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Section</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="section_heading">Section Heading <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="section_heading" type="text" name="section_heading"
                                                    value="{{ old('section_heading', $joinPage->section_heading) }}" placeholder="Enter Section Heading">
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="section_description">Section Description</label>
                                                <textarea class="form-control ckeditor-init" id="section_description" name="section_description" rows="4"
                                                    placeholder="Enter Section Description">{{ old('section_description', $joinPage->section_description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== INFO TABLE ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading d-flex justify-content-between align-items-center">
                                            <h5>Info Details</h5>
                                            <button type="button" class="btn btn-primary btn-sm" id="info-add-btn">+ Add More</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle" id="info-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 22%;">Image <span class="text-danger">*</span></th>
                                                        <th style="width: 25%;">Title <span class="text-danger">*</span></th>
                                                        <th>Description <span class="text-danger">*</span></th>
                                                        <th style="width: 90px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="info-tbody">
                                                    @forelse($infos as $i => $info)
                                                        @php $imgUrl = $info->image ? asset('home/join-us/info/'.$info->image) : ''; @endphp
                                                        <tr data-row="{{ $i }}">
                                                            <td>
                                                                <input type="hidden" name="info[{{ $i }}][id]" value="{{ $info->id }}">
                                                                <input type="file" name="info[{{ $i }}][image]" accept="image/*" class="form-control info-image-input">
                                                                <small class="text-muted">Leave blank to keep. jpg, png, webp — max 5MB.</small>
                                                                <img class="info-image-preview mt-2"
                                                                    src="{{ $imgUrl }}"
                                                                    data-existing="{{ $imgUrl }}"
                                                                    alt=""
                                                                    style="height:60px; width:80px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; {{ $imgUrl ? '' : 'display:none;' }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="info[{{ $i }}][title]" class="form-control"
                                                                    value="{{ old('info.'.$i.'.title', $info->title) }}"
                                                                    placeholder="Enter Title" required>
                                                            </td>
                                                            <td>
                                                                <textarea name="info[{{ $i }}][description]" class="form-control ckeditor-init" rows="4"
                                                                    placeholder="Enter Description">{{ old('info.'.$i.'.description', $info->description) }}</textarea>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-info-row">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        {{-- fallback: one blank row if none exist yet --}}
                                                        <tr data-row="0">
                                                            <td>
                                                                <input type="file" name="info[0][image]" accept="image/*" class="form-control info-image-input" required>
                                                                <small class="text-muted">jpg, png, webp — max 5MB</small>
                                                                <img class="info-image-preview mt-2" src="" alt=""
                                                                    style="height:60px; width:80px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:none;">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="info[0][title]" class="form-control" placeholder="Enter Title" required>
                                                            </td>
                                                            <td>
                                                                <textarea name="info[0][description]" class="form-control ckeditor-init" rows="4" placeholder="Enter Description"></textarea>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-info-row" disabled>Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== CURRENT JOB SECTION ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Current Job Section</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="current_job_title">Title <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="current_job_title" type="text" name="current_job_title"
                                                    value="{{ old('current_job_title', $joinPage->current_job_title) }}" placeholder="Enter Current Job Title">
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="current_job_description">Description <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="current_job_description" name="current_job_description" rows="6"
                                                    placeholder="Enter Description">{{ old('current_job_description', $joinPage->current_job_description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== COMMON SECTION ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Common Section</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="common_heading">Heading <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="common_heading" type="text" name="common_heading"
                                                    value="{{ old('common_heading', $joinPage->common_heading) }}" placeholder="Enter Heading">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="common_title">Title <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="common_title" type="text" name="common_title"
                                                    value="{{ old('common_title', $joinPage->common_title) }}" placeholder="Enter Title">
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="common_description">Description <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="common_description" name="common_description" rows="6"
                                                    placeholder="Enter Description">{{ old('common_description', $joinPage->common_description) }}</textarea>
                                            </div>

                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mt-2 mb-2 section-subheading">
                                                    <h6 class="mb-0">Job Rows</h6>
                                                    <button type="button" class="btn btn-primary btn-sm" id="common-add-btn">+ Add More</button>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle" id="common-table">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 25%;">Job Title <span class="text-danger">*</span></th>
                                                                <th style="width: 25%;">Subject <span class="text-danger">*</span></th>
                                                                <th>Description <span class="text-danger">*</span></th>
                                                                <th style="width: 90px;" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="common-tbody">
                                                            @forelse($commons as $i => $row)
                                                                <tr data-row="{{ $i }}">
                                                                    <td>
                                                                        <input type="hidden" name="common_rows[{{ $i }}][id]" value="{{ $row->id }}">
                                                                        <input type="text" name="common_rows[{{ $i }}][job_title]" class="form-control"
                                                                            value="{{ old('common_rows.'.$i.'.job_title', $row->job_title) }}"
                                                                            placeholder="Enter Job Title" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="common_rows[{{ $i }}][subject]" class="form-control"
                                                                            value="{{ old('common_rows.'.$i.'.subject', $row->subject) }}"
                                                                            placeholder="Enter Subject" required>
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="common_rows[{{ $i }}][description]" class="form-control ckeditor-init" rows="3"
                                                                            placeholder="Enter Description" required>{{ old('common_rows.'.$i.'.description', $row->description) }}</textarea>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-danger btn-sm remove-common-row">Remove</button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr data-row="0">
                                                                    <td>
                                                                        <input type="text" name="common_rows[0][job_title]" class="form-control" placeholder="Enter Job Title" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="common_rows[0][subject]" class="form-control" placeholder="Enter Subject" required>
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="common_rows[0][description]" class="form-control ckeditor-init" rows="3" placeholder="Enter Description" required></textarea>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-danger btn-sm remove-common-row" disabled>Remove</button>
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== EXTRA INFO ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Extra Info</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="extra_background_image">Background Image</label>
                                                <input class="form-control" id="extra_background_image" type="file" name="extra_background_image" accept=".jpg,.jpeg,.png,.webp">
                                                <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                                <div class="mt-2">
                                                    <img id="extra_background_image-preview"
                                                        src="{{ $extraUrl }}"
                                                        data-existing="{{ $extraUrl }}"
                                                        alt=""
                                                        style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $extraUrl ? '' : 'display:none;' }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="extra_description">Description <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="extra_description" name="extra_description" rows="6"
                                                    placeholder="Enter Description">{{ old('extra_description', $joinPage->extra_description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== ACTIONS ===================== --}}
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-join-page.index') }}" class="btn btn-danger px-4">Cancel</a>
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

            /* ---------- CKEditor multi-init ---------- */
            var editorInstances = new Map();

            function initCKEditor(textarea) {
                if (!textarea || editorInstances.has(textarea) || typeof ClassicEditor === 'undefined') return;
                // Strip `required` — a hidden textarea can't be focused by the browser's
                // validation bubble. Server-side rules still enforce presence.
                textarea.removeAttribute('required');
                ClassicEditor.create(textarea, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'link',
                        'bulletedList', 'numberedList', '|',
                        'alignment', 'outdent', 'indent', '|',
                        'undo', 'redo', 'removeFormat'
                    ]
                }).then(function (editor) {
                    editorInstances.set(textarea, editor);
                }).catch(function (err) { console.error(err); });
            }

            function destroyCKEditor(textarea) {
                var editor = editorInstances.get(textarea);
                if (editor) {
                    editor.destroy().catch(function (err) { console.error(err); });
                    editorInstances.delete(textarea);
                }
            }

            function initAllEditors() {
                document.querySelectorAll('.ckeditor-init').forEach(initCKEditor);
            }

            if (typeof ClassicEditor === 'undefined') {
                var tries = 0;
                var waitCk = setInterval(function () {
                    if (typeof ClassicEditor !== 'undefined' || tries++ > 40) {
                        clearInterval(waitCk);
                        initAllEditors();
                    }
                }, 100);
            } else {
                initAllEditors();
            }

            /* ---------- File input preview (with "keep existing" fallback) ---------- */
            function wirePreview(inputId, previewId) {
                var input = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        var existing = preview.getAttribute('data-existing');
                        if (existing) { preview.src = existing; preview.style.display = 'block'; }
                        else { preview.src = ''; preview.style.display = 'none'; }
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                });
            }
            wirePreview('banner_image', 'banner_image-preview');
            wirePreview('extra_background_image', 'extra_background_image-preview');

            /* ---------- INFO TABLE ---------- */
            var infoTbody  = document.getElementById('info-tbody');
            var infoAddBtn = document.getElementById('info-add-btn');
            // Next index continues past the last existing row to avoid name clashes.
            var infoIdx = infoTbody.querySelectorAll('tr').length;

            infoAddBtn.addEventListener('click', function () {
                var idx = infoIdx++;
                var tr = document.createElement('tr');
                tr.setAttribute('data-row', idx);
                tr.innerHTML =
                    '<td>' +
                        '<input type="file" name="info[' + idx + '][image]" accept="image/*" class="form-control info-image-input" required>' +
                        '<small class="text-muted">jpg, png, webp &mdash; max 5MB</small>' +
                        '<img class="info-image-preview mt-2" src="" alt="" style="height:60px; width:80px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:none;">' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" name="info[' + idx + '][title]" class="form-control" placeholder="Enter Title" required>' +
                    '</td>' +
                    '<td>' +
                        '<textarea name="info[' + idx + '][description]" class="form-control ckeditor-init" rows="4" placeholder="Enter Description"></textarea>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm remove-info-row">Remove</button>' +
                    '</td>';
                infoTbody.appendChild(tr);
                initCKEditor(tr.querySelector('.ckeditor-init'));
            });

            infoTbody.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-info-row')) return;
                if (e.target.disabled) return;
                if (!confirm('Remove this row?')) return;
                var tr = e.target.closest('tr');
                tr.querySelectorAll('.ckeditor-init').forEach(destroyCKEditor);
                tr.remove();
            });

            infoTbody.addEventListener('change', function (e) {
                if (!e.target.matches('input[type="file"].info-image-input')) return;
                var file = e.target.files && e.target.files[0];
                var preview = e.target.closest('td').querySelector('.info-image-preview');
                if (!preview) return;
                if (!file) {
                    var existing = preview.getAttribute('data-existing') || '';
                    if (existing) { preview.src = existing; preview.style.display = 'block'; }
                    else { preview.src = ''; preview.style.display = 'none'; }
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            });

            /* ---------- COMMON TABLE ---------- */
            var commonTbody  = document.getElementById('common-tbody');
            var commonAddBtn = document.getElementById('common-add-btn');
            var commonIdx = commonTbody.querySelectorAll('tr').length;

            commonAddBtn.addEventListener('click', function () {
                var idx = commonIdx++;
                var tr = document.createElement('tr');
                tr.setAttribute('data-row', idx);
                tr.innerHTML =
                    '<td>' +
                        '<input type="text" name="common_rows[' + idx + '][job_title]" class="form-control" placeholder="Enter Job Title" required>' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" name="common_rows[' + idx + '][subject]" class="form-control" placeholder="Enter Subject" required>' +
                    '</td>' +
                    '<td>' +
                        '<textarea name="common_rows[' + idx + '][description]" class="form-control ckeditor-init" rows="3" placeholder="Enter Description" required></textarea>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm remove-common-row">Remove</button>' +
                    '</td>';
                commonTbody.appendChild(tr);
                initCKEditor(tr.querySelector('.ckeditor-init'));
            });

            commonTbody.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-common-row')) return;
                if (e.target.disabled) return;
                if (!confirm('Remove this row?')) return;
                var tr = e.target.closest('tr');
                tr.querySelectorAll('.ckeditor-init').forEach(destroyCKEditor);
                tr.remove();
            });
        });
    </script>

</body>

</html>
