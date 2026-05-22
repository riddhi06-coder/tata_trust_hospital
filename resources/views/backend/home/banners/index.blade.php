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
                                        <li class="breadcrumb-item active"> Banner Details</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('banner-details.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Banner
                                </a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Heading</th>
                                            <th>Image/Video</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                        @forelse($banners as $key => $banner)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>

                                                <td>
                                                    {{ $banner->banner_heading ?? '-' }}
                                                </td>

                                                <td>
                                                    @if($banner->media_type == 'image')
                                                        
                                                        <img 
                                                            src="{{ asset('home/bannerimagevideo/' . $banner->banner_media) }}"
                                                            alt="Banner Image"
                                                            width="120"
                                                            style="border-radius:6px;"
                                                        >

                                                    @elseif($banner->media_type == 'video')

                                                        <video width="150" controls>
                                                            <source 
                                                                src="{{ asset('home/bannerimagevideo/' . $banner->banner_media) }}"
                                                                type="video/mp4"
                                                            >
                                                            Your browser does not support video.
                                                        </video>

                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    <a 
                                                        href="{{ route('banner-details.edit', $banner->id) }}"
                                                        class="btn btn-sm btn-primary"
                                                    >
                                                        Edit
                                                    </a>

                                                    <form 
                                                        action="{{ route('banner-details.destroy', $banner->id) }}"
                                                        method="POST"
                                                        style="display:inline-block;"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button 
                                                            type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this banner?')"
                                                        >
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No banners found.
                                                </td>
                                            </tr>
                                        @endforelse
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