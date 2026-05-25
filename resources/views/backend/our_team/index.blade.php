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
                                        <li class="breadcrumb-item active"> Our Team Details</li>
                                    </ol>
                                </nav>
                                <a href="{{ route('manage-our-team.create') }}" class="btn btn-primary px-5 radius-30">
                                    + Add Team Members
                                </a>
                            </div>


                            @if(session('message'))
                                <div class="alert alert-success">{{ session('message') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Designation</th>
                                            <th>Image</th>
                                            <th>Show on Home</th>
                                            <th class="text-end" style="min-width:170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($members as $i => $member)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $member->name }}</td>
                                                <td>{{ $member->designation }}</td>
                                                <td>
                                                    @if(!empty($member->image))
                                                        <img src="{{ asset('our-team/'.$member->image) }}" alt="" style="height:98px; width:88px; object-fit:cover; border-radius:50%; border:1px solid #e5e7eb;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch m-0">
                                                        <input type="checkbox"
                                                            class="form-check-input home-toggle"
                                                            id="home-toggle-{{ $member->id }}"
                                                            data-url="{{ route('manage-our-team.toggle-home', $member->id) }}"
                                                            {{ $member->show_on_home ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('manage-our-team.edit', $member->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('manage-our-team.destroy', $member->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this team member?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-4">No team members added yet.</td></tr>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var csrfToken = '{{ csrf_token() }}';

            document.querySelectorAll('.home-toggle').forEach(function (toggle) {
                toggle.addEventListener('change', function () {
                    var url       = toggle.dataset.url;
                    var nextState = toggle.checked;

                    // Disable while in-flight so users can't double-click
                    toggle.disabled = true;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept'      : 'application/json',
                        },
                        credentials: 'same-origin'
                    })
                    .then(function (res) {
                        if (!res.ok) throw new Error('Request failed');
                        return res.json();
                    })
                    .then(function (data) {
                        toggle.disabled = false;
                        // Sync with the server's actual value (in case it differs)
                        if (typeof data.show_on_home !== 'undefined') {
                            toggle.checked = !!data.show_on_home;
                        }
                        if (typeof $ !== 'undefined' && typeof $.notify === 'function') {
                            $.notify(
                                '<i class="fa fa-bell-o"></i><strong>' + (data.message || 'Updated.') + '</strong>',
                                { type: 'theme', allow_dismiss: true, delay: 3000 }
                            );
                        }
                    })
                    .catch(function () {
                        // Revert visual state on failure
                        toggle.checked = !nextState;
                        toggle.disabled = false;
                        alert('Failed to update. Please try again.');
                    });
                });
            });
        });
    </script>
</body>
</html>
