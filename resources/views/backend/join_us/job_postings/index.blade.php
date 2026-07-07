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
                                        <li class="breadcrumb-item active">Job Postings</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('manage-job-role.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Job Posting
                                </a>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Job Position</th>
                                            <th>Location</th>
                                            <th>JD</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($postings as $i => $posting)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $posting->job_position }}</td>
                                                <td>{{ $posting->job_location ?? '—' }}</td>
                                                <td>
                                                    @if($posting->jd_file)
                                                        <a href="{{ asset('home/join-us/jd/'.$posting->jd_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-job-role.edit', $posting->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-job-role.destroy', $posting->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this job posting?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">No job postings added yet.</td></tr>
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
