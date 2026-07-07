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
                                        <li class="breadcrumb-item active">Privacy Policy</li>
                                    </ol>
                                </nav>
                                @if(! $policy)
                                    <a href="{{ route('manage-privacy-policy.create') }}" class="btn btn-primary px-5 radius-30">
                                        + Add Privacy Policy
                                    </a>
                                @endif
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>File</th>
                                            <th>Uploaded</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($policy)
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <a href="{{ asset('home/privacy/'.$policy->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">View / Download</a>
                                                    <div class="text-muted small mt-1">{{ $policy->file }}</div>
                                                </td>
                                                <td>{{ optional($policy->updated_at ?? $policy->created_at)->format('d M Y, h:i A') }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-privacy-policy.edit', $policy->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-privacy-policy.destroy', $policy->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete the Privacy Policy document?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr><td colspan="4" class="text-center text-muted py-4">No Privacy Policy uploaded yet.</td></tr>
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
