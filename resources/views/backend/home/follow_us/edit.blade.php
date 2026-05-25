<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
</head>

	@include('components.backend.header')

    <!--start sidebar wrapper-->
    @include('components.backend.sidebar')
   <!--end sidebar wrapper-->


    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Follow Us</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-follow-us.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Follow Us</li>
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
                            <h4>Follow Us Form</h4>
                            <p class="f-m-light mt-1">Update the details below. Existing image is kept unless you upload a new one.</p>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @php $hasImage = !empty($follow->image); @endphp

                            <form class="row g-3 custom-input" action="{{ route('manage-follow-us.update', $follow->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Heading -->
                                <div class="col-md-6">
                                    <label class="form-label" for="title">Heading <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="title" type="text" name="title" value="{{ old('title', $follow->title) }}" placeholder="Enter Heading">
                                </div>

                                <!-- Image -->
                                <div class="col-md-6">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 2MB</small>
                                    <div class="mt-2">
                                        <img id="image-preview"
                                             src="{{ $hasImage ? asset('home/follow_us/'.$follow->image) : '' }}"
                                             alt=""
                                             data-existing="{{ $hasImage ? asset('home/follow_us/'.$follow->image) : '' }}"
                                             style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $hasImage ? '' : 'display:none;' }}">
                                    </div>
                                </div>

                                <!-- Social Media Link -->
                                <div class="col-md-12">
                                    <label class="form-label" for="social_media_link">Social Media Link <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="social_media_link" type="url" name="social_media_link" value="{{ old('social_media_link', $follow->social_media_link) }}" placeholder="https://www.facebook.com/yourpage">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <label class="form-label">Description <span class="txt-danger">*</span></label>
                                    <textarea name="description" id="editor" class="form-control" rows="6" placeholder="Enter Description">{{ old('description', $follow->description) }}</textarea>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-follow-us.index') }}" class="btn btn-danger px-4">Cancel</a>
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
    </div>

    @include('components.backend.main-js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input   = document.getElementById('image');
            var preview = document.getElementById('image-preview');

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
