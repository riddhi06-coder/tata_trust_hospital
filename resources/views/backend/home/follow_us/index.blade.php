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
                                        <li class="breadcrumb-item active"> Follow Us Details</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('manage-follow-us.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Follow Us
                                </a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Heading</th>
                                            <th>Image</th>
                                            <th>Social Media Link</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($followUs as $i => $follow)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $follow->title }}</td>
                                                <td>
                                                    @if(!empty($follow->image))
                                                        <img src="{{ asset('home/follow_us/'.$follow->image) }}" alt="" style="height:48px; width:72px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($follow->social_media_link))
                                                        <a href="{{ $follow->social_media_link }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width:280px;">
                                                            {{ $follow->social_media_link }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-follow-us.edit', $follow->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-follow-us.destroy', $follow->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this Follow Us entry?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-4">No Follow Us entries added yet.</td></tr>
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
