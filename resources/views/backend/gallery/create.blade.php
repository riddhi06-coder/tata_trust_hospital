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
                    <div class="col-6"><h4>Add Gallery Images</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-gallery.index') }}">Gallery</a></li>
                            <li class="breadcrumb-item active">Add Images</li>
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
                            <h4>Upload Images</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — set the banner details and upload your first images.</p>
                            @else
                                <p class="f-m-light mt-1">Drag &amp; drop or click below. You can select many images at once.</p>
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

                            <form id="galleryUploadForm" class="custom-input" action="{{ route('manage-gallery.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                @if($showBanner)
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label" for="banner_heading">Banner Heading</label>
                                            <input class="form-control" id="banner_heading" type="text" name="banner_heading"
                                                value="{{ old('banner_heading', $gallery->banner_heading ?? '') }}"
                                                placeholder="Enter Banner Heading">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="banner_media">Banner Image</label>
                                            <input class="form-control" id="banner_media" type="file" name="banner_media"
                                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm">
                                            <small class="text-muted">jpg, jpeg, png, webp, mp4, webm — max 10MB</small>
                                            <div class="banner-media-preview mt-2"></div>
                                        </div>
                                    </div>
                                    <hr>
                                @endif

                                <label for="images" class="dropzone-area" id="dropzone">
                                    <div class="dz-icon">📤</div>
                                    <div><strong>Click to select</strong> or drop images here</div>
                                    <small class="text-muted d-block mt-1">jpg, jpeg, png, webp — max 5MB each</small>
                                    <input class="d-none" id="images" type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                                </label>

                                <div class="mt-3">
                                    <span class="selected-summary" id="selectedSummary">No files selected</span>
                                </div>

                                <div class="file-preview-grid" id="previewGrid"></div>

                                <div class="mt-4 text-end">
                                    <a href="{{ route('manage-gallery.index') }}" class="btn btn-danger px-4">Cancel</a>
                                    <button class="btn btn-primary" type="submit" id="submitBtn" disabled>Upload</button>
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
            var input       = document.getElementById('images');
            var dropzone    = document.getElementById('dropzone');
            var previewGrid = document.getElementById('previewGrid');
            var summary     = document.getElementById('selectedSummary');
            var submitBtn   = document.getElementById('submitBtn');

            var store = new DataTransfer();

            function renderPreviews() {
                previewGrid.innerHTML = '';
                var files = Array.from(store.files);

                files.forEach(function (file, idx) {
                    var tile = document.createElement('div');
                    tile.className = 'preview-tile';

                    var img = document.createElement('img');
                    img.alt = file.name;
                    img.src = URL.createObjectURL(file);

                    var meta = document.createElement('div');
                    meta.className = 'meta';
                    meta.textContent = file.name;

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '×';
                    removeBtn.title = 'Remove';
                    removeBtn.addEventListener('click', function () {
                        removeFileAt(idx);
                    });

                    tile.appendChild(removeBtn);
                    tile.appendChild(img);
                    tile.appendChild(meta);
                    previewGrid.appendChild(tile);
                });

                summary.textContent = files.length === 0
                    ? 'No files selected'
                    : files.length + ' file' + (files.length === 1 ? '' : 's') + ' ready to upload';

                submitBtn.disabled = files.length === 0;
                input.files = store.files;
            }

            function addFiles(fileList) {
                Array.from(fileList).forEach(function (f) {
                    if (!/^image\//.test(f.type)) return;
                    store.items.add(f);
                });
                renderPreviews();
            }

            function removeFileAt(index) {
                var next = new DataTransfer();
                Array.from(store.files).forEach(function (f, i) {
                    if (i !== index) next.items.add(f);
                });
                store = next;
                renderPreviews();
            }

            input.addEventListener('change', function () { addFiles(input.files); });

            ['dragenter', 'dragover'].forEach(function (ev) {
                dropzone.addEventListener(ev, function (e) {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.add('is-dragging');
                });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                dropzone.addEventListener(ev, function (e) {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.remove('is-dragging');
                });
            });
            dropzone.addEventListener('drop', function (e) {
                if (e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files);
            });

            // Banner media live preview
            var bannerInput = document.getElementById('banner_media');
            var bannerPreview = document.querySelector('.banner-media-preview');
            if (bannerInput && bannerPreview) {
                bannerInput.addEventListener('change', function () {
                    var file = bannerInput.files && bannerInput.files[0];
                    if (!file) { bannerPreview.innerHTML = ''; return; }
                    var isVideo = /^video\//.test(file.type);
                    var url = URL.createObjectURL(file);
                    bannerPreview.innerHTML = isVideo
                        ? '<video controls><source src="'+url+'"></video>'
                        : '<img src="'+url+'" alt="preview">';
                });
            }
        });
    </script>
</body>
</html>
