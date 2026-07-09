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
                                        <li class="breadcrumb-item active">Blog Listing</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('manage-blogs-listing.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Blog
                                </a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Category</th>
                                            <th>Thumbnail</th>
                                            <th>Title</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($listings as $i => $listing)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $listing->category->name ?? '—' }}</td>
                                                <td>
                                                    @if($listing->thumbnail)
                                                        <img src="{{ asset('home/blog/thumbnails/'.$listing->thumbnail) }}" alt="{{ $listing->title }}" style="height:100px; width:100px; object-fit:cover; border-radius:4px;">
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $listing->title }}
                                                </td>
                                               
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-blogs-listing.edit', $listing->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-blogs-listing.destroy', $listing->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this blog?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-4">No blogs added yet.</td></tr>
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
