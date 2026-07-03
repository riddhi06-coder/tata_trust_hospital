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
                                        <li class="breadcrumb-item active">Speciality Details</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('speciality-details.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Speciality Details
                                </a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Speciality</th>
                                            <th>Section Heading</th>
                                            <th>Banner Image</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($details as $i => $detail)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $detail->speciality?->speciality ?? '—' }}</td>
                                                <td>{{ $detail->section_heading }}</td>
                                                <td>
                                                    @if(!empty($detail->banner_image))
                                                        <img src="{{ asset('home/speciality-details/'.$detail->banner_image) }}" alt=""
                                                            style="height:64px; width:96px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                               
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('speciality-details.edit', $detail->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('speciality-details.destroy', $detail->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this record?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">No speciality details added yet.</td></tr>
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
