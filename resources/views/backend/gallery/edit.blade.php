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
                    <div class="col-6"><h4>Edit Gallery Image</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-gallery.index') }}">Gallery</a></li>
                            <li class="breadcrumb-item active">Edit Image</li>
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
                            <h4>Update Image</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — banner details are edited here.</p>
                            @else
                                <p class="f-m-light mt-1">Existing image is kept unless you upload a new one.</p>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-gallery.update', $image->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                @if($showBanner)
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
                                        <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp, mp4, webm — max 10MB.</small>

                                        <div class="banner-media-preview mt-2">
                                            @if($gallery && !empty($gallery->banner_media))
                                                @if($gallery->media_type === 'video')
                                                    <video controls>
                                                        <source src="{{ asset('home/gallery/'.$gallery->banner_media) }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <img src="{{ asset('home/gallery/'.$gallery->banner_media) }}" alt="Banner Preview">
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="section_heading">Section Heading</label>
                                        <input class="form-control" id="section_heading" type="text" name="section_heading"
                                            value="{{ old('section_heading', $gallery->section_heading ?? '') }}"
                                            placeholder="Enter Section Heading">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="editor">Section Description</label>
                                        <textarea class="form-control" id="editor" name="section_description" rows="6"
                                            placeholder="Enter Section Description">{{ old('section_description', $gallery->section_description ?? '') }}</textarea>
                                    </div>

                                    <div class="col-12"><hr></div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label" for="image">Replace Image</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control">
                                    <small class="text-muted">jpg, jpeg, png, webp — max 5MB. Leave blank to keep current.</small>

                                    <div class="mt-2">
                                        <img id="image-preview"
                                             src="{{ asset('home/gallery/'.$image->image) }}"
                                             data-existing="{{ asset('home/gallery/'.$image->image) }}"
                                             alt=""
                                             style="max-height:200px; max-width:280px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_on_home"
                                            name="show_on_home" value="1"
                                            {{ old('show_on_home', $image->show_on_home) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="show_on_home">Show on Home</label>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-gallery.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            var input   = document.getElementById('image');
            var preview = document.getElementById('image-preview');

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

            // Banner media live preview
            var bannerInput = document.getElementById('banner_media');
            var bannerPreview = document.querySelector('.banner-media-preview');
            if (bannerInput && bannerPreview) {
                bannerInput.addEventListener('change', function () {
                    var file = bannerInput.files && bannerInput.files[0];
                    if (!file) return;
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
