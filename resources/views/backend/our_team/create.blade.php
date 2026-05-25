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
                    <div class="col-6"><h4>Add Team Member</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-our-team.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Team Member</li>
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
                            <h4>Team Member Form</h4>
                            <p class="f-m-light mt-1">Fill in the details below.</p>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-our-team.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Member Name -->
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Member Name <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Dr. John Doe">
                                </div>

                                <!-- Image -->
                                <div class="col-md-6">
                                    <label class="form-label" for="image">Image <span class="txt-danger">*</span></label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control" required>
                                    <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
                                    <div class="mt-2">
                                        <img id="image-preview" src="" alt="" style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                    </div>
                                </div>

                                <!-- Designation -->
                                <div class="col-md-6">
                                    <label class="form-label" for="designation">Designation <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="designation" type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Chief Veterinarian">
                                </div>

                                <!-- Education (optional) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="education">Education</label>
                                    <input class="form-control" id="education" type="text" name="education" value="{{ old('education') }}" placeholder="e.g. BVSc & AH, MVSc">
                                </div>

                                <!-- Social Media Link -->
                                <div class="col-md-12">
                                    <label class="form-label" for="social_media_link">Social Media Link <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="social_media_link" type="url" name="social_media_link" value="{{ old('social_media_link') }}" placeholder="https://www.linkedin.com/in/yourprofile">
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-our-team.index') }}" class="btn btn-danger px-4">Cancel</a>
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
    </div>

    @include('components.backend.main-js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input   = document.getElementById('image');
            var preview = document.getElementById('image-preview');

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
        });
    </script>

</body>

</html>
