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
                    <div class="col-6"><h4>Edit FAQ</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-faqs.index') }}">FAQs</a></li>
                            <li class="breadcrumb-item active">Edit FAQ</li>
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
                            <h4>Update FAQ</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — banner and section details are edited here.</p>
                            @else
                                <p class="f-m-light mt-1">Update the question and answer below.</p>
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

                            <form class="row g-4 custom-input" action="{{ route('manage-faqs.update', $faq->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

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
                                        <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                        <div class="mt-2">
                                            @php $hasBanner = $settings && !empty($settings->banner_image); @endphp
                                            <img id="banner_image-preview"
                                                src="{{ $hasBanner ? asset('home/faq/'.$settings->banner_image) : '' }}"
                                                data-existing="{{ $hasBanner ? asset('home/faq/'.$settings->banner_image) : '' }}"
                                                alt=""
                                                style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $hasBanner ? '' : 'display:none;' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="section_heading">Section Heading</label>
                                        <input class="form-control" id="section_heading" type="text" name="section_heading"
                                            value="{{ old('section_heading', $settings->section_heading ?? '') }}"
                                            placeholder="Enter Section Heading">
                                    </div>

                                    <div class="col-12"><hr></div>
                                @endif

                                <div class="col-md-12">
                                    <label class="form-label" for="question">Question <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="question" type="text" name="question"
                                        value="{{ old('question', $faq->question) }}"
                                        placeholder="Enter Question" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="editor">Answer <span class="txt-danger">*</span></label>
                                    <textarea class="form-control" id="editor" name="answer" rows="6"
                                        placeholder="Enter Answer">{{ old('answer', $faq->answer) }}</textarea>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-faqs.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            var input   = document.getElementById('banner_image');
            var preview = document.getElementById('banner_image-preview');
            if (!input || !preview) return;

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) {
                    var existing = preview.getAttribute('data-existing');
                    if (existing) {
                        preview.src = existing;
                        preview.style.display = 'block';
                    } else {
                        preview.src = '';
                        preview.style.display = 'none';
                    }
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
