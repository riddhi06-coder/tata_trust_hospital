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
                    <div class="col-6"><h4>Add Testimonial</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-master-testimonials.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Testimonial</li>
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
                            <h4>Testimonial Form</h4>
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

                            <form class="row g-3 custom-input" action="{{ route('manage-master-testimonials.store') }}" method="POST">
                                @csrf

                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Name <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe">
                                </div>

                                <!-- Active toggle -->
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active <small class="text-muted">(only active testimonials are shown)</small></label>
                                    </div>
                                </div>

                                <!-- Testimony -->
                                <div class="col-md-12">
                                    <label class="form-label" for="testimony">Testimony <span class="txt-danger">*</span></label>
                                    <textarea name="testimony" id="editor" class="form-control" rows="6" placeholder="Write the testimony here…">{{ old('testimony') }}</textarea>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-master-testimonials.index') }}" class="btn btn-danger px-4">Cancel</a>
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

</body>

</html>
