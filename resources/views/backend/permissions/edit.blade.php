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
        <div class="page-title"><div class="row"><div class="col-12"><h3>Manage Permissions — {{ $role->name }}</h3></div></div></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.permissions.update', $role) }}" method="POST" class="theme-form">
                            @csrf @method('PUT')

                            <p class="text-muted">Tick the actions this role is allowed to perform. Cards mirror the sidebar.</p>

                            @php
                                // Column order + labels used across every card.
                                $actionLabels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'assign' => 'Assign'];
                            @endphp

                            <div class="row">
                                @foreach($permissions as $module => $perms)
                                    @php
                                        // Rows = sub-items (grouped by slug prefix, e.g. "home-banners").
                                        $subItems = $perms->groupBy(fn ($p) => \Illuminate\Support\Str::beforeLast($p->slug, '.'));
                                        // Columns = only the actions that actually exist in this card, in a fixed order.
                                        $present  = $perms->map(fn ($p) => \Illuminate\Support\Str::afterLast($p->slug, '.'))->unique();
                                        $cols     = collect(array_keys($actionLabels))->filter(fn ($a) => $present->contains($a))
                                                        ->merge($present->diff(array_keys($actionLabels)))->values();
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <div class="card border h-100">
                                            <div class="card-header perm-card-header py-2 d-flex justify-content-between align-items-center">
                                                <strong>{{ $module }}</strong>
                                                <label class="form-check-label small mb-0" style="cursor:pointer;">
                                                    <input type="checkbox" class="form-check-input module-toggle me-1" data-module="{{ $module }}">
                                                    Select all
                                                </label>
                                            </div>
                                            <table class="table table-sm table-hover mb-0 align-middle perm-matrix">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-3 sec-col">Section</th>
                                                        @foreach($cols as $col)
                                                            <th class="text-center act-col">{{ $actionLabels[$col] ?? ucfirst($col) }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($subItems as $prefix => $items)
                                                        @php $subLabel = preg_replace('/^(View|Create|Edit|Delete|Assign)\s+/', '', $items->first()->name); @endphp
                                                        <tr>
                                                            <td class="ps-3 sec-col">{{ $subLabel }}</td>
                                                            @foreach($cols as $col)
                                                                @php $perm = $items->first(fn ($p) => \Illuminate\Support\Str::afterLast($p->slug, '.') === $col); @endphp
                                                                <td class="text-center act-col">
                                                                    @if($perm)
                                                                        <input
                                                                            type="checkbox"
                                                                            name="permissions[]"
                                                                            value="{{ $perm->id }}"
                                                                            id="perm-{{ $perm->id }}"
                                                                            class="form-check-input perm-checkbox"
                                                                            data-module="{{ $module }}"
                                                                            title="{{ $perm->slug }}"
                                                                            {{ in_array($perm->id, $assigned) ? 'checked' : '' }}>
                                                                    @else
                                                                        <span class="text-muted">&mdash;</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
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

<script>
    document.querySelectorAll('.module-toggle').forEach(function (master) {
        master.addEventListener('change', function () {
            var module = master.dataset.module;
            document.querySelectorAll('.perm-checkbox[data-module="' + module + '"]').forEach(function (cb) {
                cb.checked = master.checked;
            });
        });
    });
</script>
</body>
</html>
