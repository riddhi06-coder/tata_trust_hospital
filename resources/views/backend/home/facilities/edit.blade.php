<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
</head>

	@include('components.backend.header')

    <!--start sidebar wrapper-->
    @include('components.backend.sidebar')
   <!--end sidebar wrapper-->


    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Facilities</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-facilities.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Facilities</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Facilities Form</h4>
                            <p class="f-m-light mt-1">Update the details below. Existing images are kept unless you upload a new one.</p>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @php
                                $existingRows = $facility->facilities ?? [];
                            @endphp

                            <form class="row g-3 custom-input" action="{{ route('manage-facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data" id="specialities-form">
                                @csrf
                                @method('PUT')

                                <!-- Title -->
                                <div class="col-md-6">
                                    <label class="form-label" for="title">Title <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="title" type="text" name="title" value="{{ old('title', $facility->title) }}" placeholder="Enter Title">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <label class="form-label">Description <span class="txt-danger">*</span></label>
                                    <textarea name="description" id="editor" class="form-control" rows="6" placeholder="Enter Description">{{ old('description', $facility->description) }}</textarea>
                                </div>

                                <!-- Facilities Table -->
                                <div class="col-12 mt-4">
                                    <h5 class="mb-2">Facilities</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="specialities-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%;">Image <span class="text-danger">*</span></th>
                                                    <th style="width: 50%;">Facility Name <span class="text-danger">*</span></th>
                                                    <th style="width: 20%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="specialities-tbody">
                                                @forelse($existingRows as $idx => $row)
                                                    @php $hasIcon = !empty($row['icon']); @endphp
                                                    <tr data-row="{{ $idx }}">
                                                        <td>
                                                            @if($hasIcon)
                                                                <input type="hidden" name="existing_icons[{{ $idx }}]" value="{{ $row['icon'] }}">
                                                            @endif
                                                            <input type="file" name="icons[{{ $idx }}]" accept="image/*" class="form-control icon-input">
                                                            <small class="text-muted">Leave blank to keep current. jpg, png, webp, svg — max 2MB</small>
                                                            <img class="icon-preview mt-2"
                                                                 src="{{ $hasIcon ? asset('home/facilities/'.$row['icon']) : '' }}"
                                                                 alt="{{ $row['name'] ?? '' }}"
                                                                 style="height:48px; width:48px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; {{ $hasIcon ? '' : 'display:none;' }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="names[{{ $idx }}]" class="form-control" value="{{ old('names.'.$idx, $row['name'] ?? '') }}" placeholder="e.g. Pharmacy" required>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($loop->first)
                                                                <button type="button" id="add-more-btn" class="btn btn-primary btn-sm">+ Add More</button>
                                                            @else
                                                                <button type="button" class="btn btn-danger btn-sm remove-row-btn">Remove</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-row="0">
                                                        <td>
                                                            <input type="file" name="icons[0]" accept="image/*" class="form-control icon-input" required>
                                                            <small class="text-muted">jpg, png, webp, svg — max 2MB</small>
                                                            <img class="icon-preview mt-2" src="" alt="" style="height:48px; width:48px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:none;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="names[0]" class="form-control" placeholder="e.g. Pharmacy" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" id="add-more-btn" class="btn btn-primary btn-sm">+ Add More</button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-facilities.index') }}" class="btn btn-danger px-4">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            var tbody  = document.getElementById('specialities-tbody');
            var addBtn = document.getElementById('add-more-btn');

            var rows    = tbody.querySelectorAll('tr[data-row]');
            var nextIdx = 0;
            rows.forEach(function (r) {
                var i = parseInt(r.getAttribute('data-row'), 10);
                if (!isNaN(i) && i >= nextIdx) nextIdx = i + 1;
            });

            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    var idx = nextIdx++;
                    var tr = document.createElement('tr');
                    tr.setAttribute('data-row', idx);
                    tr.innerHTML =
                        '<td>' +
                            '<input type="file" name="icons[' + idx + ']" accept="image/*" class="form-control icon-input" required>' +
                            '<small class="text-muted">jpg, png, webp, svg &mdash; max 2MB</small>' +
                            '<img class="icon-preview mt-2" src="" alt="" style="height:48px; width:48px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:none;">' +
                        '</td>' +
                        '<td>' +
                            '<input type="text" name="names[' + idx + ']" class="form-control" placeholder="e.g. Pharmacy" required>' +
                        '</td>' +
                        '<td class="text-center">' +
                            '<button type="button" class="btn btn-danger btn-sm remove-row-btn">Remove</button>' +
                        '</td>';
                    tbody.appendChild(tr);
                });
            }

            tbody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row-btn')) {
                    e.target.closest('tr').remove();
                }
            });

            // Live preview
            tbody.addEventListener('change', function (e) {
                if (!e.target.matches('input[type="file"].icon-input')) return;

                var file = e.target.files && e.target.files[0];
                var preview = e.target.closest('td').querySelector('.icon-preview');
                if (!preview) return;

                if (!file) {
                    var existing = e.target.closest('td').querySelector('input[name^="existing_icons"]');
                    if (existing && existing.value) {
                        preview.src = "{{ asset('home/facilities') }}/" + existing.value;
                        preview.style.display = 'block';
                    } else {
                        preview.src = '';
                        preview.style.display = 'none';
                    }
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

</body>

</html>
