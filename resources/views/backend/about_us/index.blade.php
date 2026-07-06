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
                    <div class="col-6"></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon">
                                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                                    </svg>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item active">About Us</li>
                                    </ol>
                                </nav>
                                @if(!$about)
                                    <a href="{{ route('manage-about-us.create') }}" class="btn btn-primary px-5 radius-30">
                                        + Add About Us
                                    </a>
                                @endif
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Banner Heading</th>
                                            <th>Banner Image</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($about)
                                            <tr>
                                                <td>1</td>
                                                <td>{{ $about->banner_heading ?: '—' }}</td>
                                                <td>
                                                    @if(!empty($about->banner_image))
                                                        <img src="{{ asset('about_us/'.$about->banner_image) }}" alt=""
                                                            style="height:64px; width:96px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-about-us.edit', $about->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-about-us.destroy', $about->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete the About Us content?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr><td colspan="4" class="text-center text-muted py-4">No About Us content added yet.</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
