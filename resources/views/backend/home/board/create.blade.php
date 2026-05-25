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
                    <div class="col-6"><h4>Add Our Boards</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-board.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Our Boards</li>
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
                            <h4>Our Boards Form</h4>
                            <p class="f-m-light mt-1">Fill in the details below.</p>
                        </div>
                        <div class="card-body">

                            <form class="row g-3 custom-input" action="{{ route('manage-board.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Heading -->
                                <div class="col-md-6">
                                    <label class="form-label" for="title">Heading <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="title" type="text" name="title" value="{{ old('title') }}" placeholder="Enter Heading">
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

                                <!-- Description -->
                                <div class="col-md-12">
                                    <label class="form-label">Description <span class="txt-danger">*</span></label>
                                    <textarea name="description" id="editor" class="form-control" rows="6" placeholder="Enter Description">{{ old('description') }}</textarea>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-board.index') }}" class="btn btn-danger px-4">Cancel</a>
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
