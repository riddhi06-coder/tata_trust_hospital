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
                    <div class="col-6"><h4>Edit Flyer</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-flyer.index') }}">Flyers</a></li>
                            <li class="breadcrumb-item active">Edit Flyer</li>
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
                            <h4>Flyer Form</h4>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-flyer.update', $flyer->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label" for="flyer_image">Flyer Image</label>
                                    <input type="file" id="flyer_image" name="flyer_image" accept="image/*" class="form-control">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB</small>
                                    <div class="mt-2">
                                        @if($flyer->flyer_image)
                                            <img id="flyer_image-current" src="{{ asset('home/flyer/'.$flyer->flyer_image) }}" alt="Flyer Image"
                                                style="max-height:180px; max-width:280px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                        @endif
                                        <img id="flyer_image-preview" src="" alt=""
                                            style="max-height:180px; max-width:280px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-flyer.index') }}" class="btn btn-danger px-4">Cancel</a>
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
        (function () {
            var input = document.getElementById('flyer_image');
            var preview = document.getElementById('flyer_image-preview');
            var current = document.getElementById('flyer_image-current');
            if (!input || !preview) return;
            input.addEventListener('change', function () {
                var f = input.files && input.files[0];
                if (!f) {
                    preview.style.display = 'none';
                    preview.src = '';
                    if (current) current.style.display = '';
                    return;
                }
                preview.src = URL.createObjectURL(f);
                preview.style.display = 'block';
                if (current) current.style.display = 'none';
            });
        })();
    </script>

</body>
</html>
