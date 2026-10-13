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
                <div class="row align-items-center">
                    <div class="col-6"><h4>Chatbot Management</h4></div>
                    <div class="col-6 text-end">
                        <a href="{{ route('manage-chatbot.create') }}" class="btn btn-primary btn-sm px-3">+ Add new item</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            @if(session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-light border">
                <i class="fa fa-info-circle me-1"></i>
                Edit the WhatsApp chatbot's questions, answer options (and their order), menu labels, info blocks and FAQ answers here.
                The conversation flow &amp; branching stay fixed in code — this controls the wording and options shown to pet parents.
                Speciality &amp; doctor options are drawn live from the <a href="{{ route('manage-our-team.index') }}">Our Team</a> list.
            </div>

            @foreach($sections as $section => $items)
                <div class="card mt-2">
                    <div class="card-header py-3">
                        <h5 class="mb-0">{{ $section }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:70px">Priority</th>
                                        <th style="width:26%">Item</th>
                                        <th>Preview</th>
                                        <th style="width:80px">Options</th>
                                        <th style="width:90px">Status</th>
                                        <th style="width:90px" class="text-end">Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="text-muted">{{ $item->sort_order }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $item->title }}</div>
                                                @unless($item->is_protected)<small class="text-muted">custom</small>@endunless
                                            </td>
                                            <td><small class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 90) ?: ($item->has_options ? '—' : '') }}</small></td>
                                            <td>{{ $item->has_options ? count($item->options ?? []) : '—' }}</td>
                                            <td>
                                                @if($item->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Hidden</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('manage-chatbot.edit', $item->id) }}" class="btn btn-sm btn-primary py-1 px-2">Edit</a>
                                                @unless($item->is_protected)
                                                    <form action="{{ route('manage-chatbot.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this custom question?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">Delete</button>
                                                    </form>
                                                @endunless
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
