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
                    <div class="col-6"><h4>Edit Event</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-events.index') }}">Events</a></li>
                            <li class="breadcrumb-item active">Edit Event</li>
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
                            <h4>Update Event</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — section heading is edited here.</p>
                            @else
                                <p class="f-m-light mt-1">Existing images are kept unless you upload new ones.</p>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                @if($showBanner)
                                    <div class="col-md-12">
                                        <label class="form-label" for="section_heading">Section Heading</label>
                                        <input class="form-control" id="section_heading" type="text" name="section_heading"
                                            value="{{ old('section_heading', $settings->section_heading ?? '') }}"
                                            placeholder="Enter Section Heading">
                                    </div>
                                    <div class="col-12"><hr></div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label" for="title">Event Title <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="title" type="text" name="title"
                                        value="{{ old('title', $event->title) }}"
                                        placeholder="Enter Event Title">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="month">Month</label>
                                    <select class="form-control" id="month" name="month">
                                        <option value="">— Select Month —</option>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" {{ (string) old('month', $event->month) === (string) $num ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="year">Year</label>
                                    <select class="form-control" id="year" name="year">
                                        <option value="">— Select Year —</option>
                                        @foreach($years as $yr)
                                            <option value="{{ $yr }}" {{ (string) old('year', $event->year) === (string) $yr ? 'selected' : '' }}>
                                                {{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="thumbnail">Replace Thumbnail</label>
                                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="form-control">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 5MB.</small>
                                    <div class="mt-2">
                                        <img id="thumbnail-preview"
                                            src="{{ asset('home/events/'.$event->thumbnail) }}"
                                            data-existing="{{ asset('home/events/'.$event->thumbnail) }}"
                                            alt=""
                                            style="max-height:140px; max-width:220px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="image">Replace Event Image</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="image-preview"
                                            src="{{ asset('home/events/'.$event->image) }}"
                                            data-existing="{{ asset('home/events/'.$event->image) }}"
                                            alt=""
                                            style="max-height:180px; max-width:280px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <div class="col-md-12 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_on_home"
                                            name="show_on_home" value="1"
                                            {{ old('show_on_home', $event->show_on_home) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="show_on_home">Show on Home</label>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-events.index') }}" class="btn btn-danger px-4">Cancel</a>
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

            wirePreview('thumbnail', 'thumbnail-preview');
            wirePreview('image',     'image-preview');
        });
    </script>
</body>
</html>
