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
        .section-heading h5 { margin: 0 !important; font-weight: 700; color: #1e3a8a; font-size: 1.05rem; }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Add Blog</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-blogs-listing.index') }}">Blogs</a></li>
                            <li class="breadcrumb-item active">Add Blog</li>
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
                            <h4>Blog Form</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first blog — set the banner details, then fill in the blog itself.</p>
                            @else
                                <p class="f-m-light mt-1">Fill in the blog details below.</p>
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

                            <form class="row g-4 custom-input" action="{{ route('manage-blogs-listing.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                @if($showBanner)
                                    {{-- ===================== BANNER (only for first blog) ===================== --}}
                                    <div class="col-12">
                                        <div class="border rounded p-3">
                                            <div class="section-heading"><h5>Banner</h5></div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="banner_heading">Banner Heading</label>
                                                    <input class="form-control" id="banner_heading" type="text" name="banner_heading"
                                                        value="{{ old('banner_heading', $settings->banner_heading ?? '') }}"
                                                        placeholder="Enter Banner Heading">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label" for="banner_image">Banner Image</label>
                                                    <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
                                                    <small class="text-muted">jpg, jpeg, png, webp — max 10MB</small>
                                                    <div class="mt-2">
                                                        <img id="banner_image-preview" src="" alt=""
                                                            style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ===================== BLOG DETAILS ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Blog Details</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="blog_category_id">Category <span class="txt-danger">*</span></label>
                                                <select class="form-select" id="blog_category_id" name="blog_category_id">
                                                    <option value="">— Select Category —</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ (string) old('blog_category_id') === (string) $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="blog_date">Date</label>
                                                <input class="form-control" id="blog_date" type="date" name="blog_date"
                                                    value="{{ old('blog_date', now()->format('Y-m-d')) }}">
                                                <small class="text-muted">Defaults to today if left blank.</small>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="title">Blog Title <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="title" type="text" name="title"
                                                    value="{{ old('title') }}" placeholder="Enter Blog Title">
                                                <small class="text-muted">Slug will be auto-generated from the title.</small>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="thumbnail">Thumbnail Image <span class="txt-danger">*</span></label>
                                                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="form-control">
                                                <small class="text-muted">jpg, jpeg, png, webp — max 5MB.</small>
                                                <div class="mt-2">
                                                    <img id="thumbnail-preview" src="" alt=""
                                                        style="max-height:140px; max-width:220px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="short_description">Short Description <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="short_description" name="short_description" rows="6"
                                                    placeholder="Enter Short Description">{{ old('short_description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== TAGS ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading d-flex justify-content-between align-items-center">
                                            <h5>Blog Tags</h5>
                                            <button type="button" class="btn btn-primary btn-sm" id="tag-add-btn">+ Add More</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle" id="tag-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Tag</th>
                                                        <th style="width: 90px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tag-tbody">
                                                    <tr data-row="0">
                                                        <td>
                                                            <input type="text" name="tags[0][tag]" class="form-control" placeholder="e.g. Pet Care, Feeding Tips, Puppies">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-tag-row" disabled title="Blank tags are ignored">Remove</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== ACTIONS ===================== --}}
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-blogs-listing.index') }}" class="btn btn-danger px-4">Cancel</a>
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

            /* ---------- CKEditor multi-init ---------- */
            var editorInstances = new Map();
            function initCKEditor(textarea) {
                if (!textarea || editorInstances.has(textarea) || typeof ClassicEditor === 'undefined') return;
                textarea.removeAttribute('required');
                ClassicEditor.create(textarea, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'link',
                        'bulletedList', 'numberedList', '|',
                        'alignment', 'outdent', 'indent', '|',
                        'undo', 'redo', 'removeFormat'
                    ]
                }).then(function (editor) { editorInstances.set(textarea, editor); })
                  .catch(function (err) { console.error(err); });
            }
            function initAllEditors() { document.querySelectorAll('.ckeditor-init').forEach(initCKEditor); }
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

            /* ---------- File input previews ---------- */
            function wirePreview(inputId, previewId) {
                var input = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) { preview.src = ''; preview.style.display = 'none'; return; }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                });
            }
            wirePreview('banner_image', 'banner_image-preview');
            wirePreview('thumbnail', 'thumbnail-preview');

            /* ---------- Tag table: add / remove rows ---------- */
            var tagTbody  = document.getElementById('tag-tbody');
            var tagAddBtn = document.getElementById('tag-add-btn');
            var tagIdx = 1;

            tagAddBtn.addEventListener('click', function () {
                var idx = tagIdx++;
                var tr = document.createElement('tr');
                tr.setAttribute('data-row', idx);
                tr.innerHTML =
                    '<td>' +
                        '<input type="text" name="tags[' + idx + '][tag]" class="form-control" placeholder="e.g. Pet Care, Feeding Tips, Puppies">' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm remove-tag-row">Remove</button>' +
                    '</td>';
                tagTbody.appendChild(tr);
            });

            tagTbody.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-tag-row')) return;
                if (e.target.disabled) return;
                e.target.closest('tr').remove();
            });
        });
    </script>

</body>

</html>
