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
                    <div class="col-6"><h4>Edit Specialities</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home-specialities.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Specialities</li>
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
                            <h4>Specialities Form</h4>
                            <p class="f-m-light mt-1">Update the details below. Existing icons are kept unless you upload a new one.</p>
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
                                $existingRows = $speciality->specialities ?? [];
                            @endphp

                            <form class="row g-3 custom-input" action="{{ route('home-specialities.update', $speciality->id) }}" method="POST" enctype="multipart/form-data" id="specialities-form">
                                @csrf
                                @method('PUT')

                                <!-- Our Motto -->
                                <div class="col-md-6">
                                    <label class="form-label" for="our_motto">Our Motto <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="our_motto" type="text" name="our_motto" value="{{ old('our_motto', $speciality->our_motto) }}" placeholder="Enter Our Motto">
                                </div>

                                <!-- Title -->
                                <div class="col-md-6">
                                    <label class="form-label" for="title">Title <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="title" type="text" name="title" value="{{ old('title', $speciality->title) }}" placeholder="Enter Title">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <label class="form-label">Description <span class="txt-danger">*</span></label>
                                    <textarea name="description" id="editor" class="form-control" rows="6" placeholder="Enter Description">{{ old('description', $speciality->description) }}</textarea>
                                </div>

                                <!-- Specialities Table -->
                                <div class="col-12 mt-4">
                                    <h5 class="mb-2">Specialities</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="specialities-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%;">Icon <span class="text-danger">*</span></th>
                                                    <th style="width: 50%;">Speciality Name <span class="text-danger">*</span></th>
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
                                                                 src="{{ $hasIcon ? asset('home/specialities/'.$row['icon']) : '' }}"
                                                                 alt="{{ $row['name'] ?? '' }}"
                                                                 style="height:48px; width:48px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; {{ $hasIcon ? '' : 'display:none;' }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="names[{{ $idx }}]" class="form-control" value="{{ old('names.'.$idx, $row['name'] ?? '') }}" placeholder="e.g. Cardiology" required>
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
                                                    {{-- Fall back to one empty row if the record somehow has no specialities --}}
                                                    <tr data-row="0">
                                                        <td>
                                                            <input type="file" name="icons[0]" accept="image/*" class="form-control icon-input" required>
                                                            <small class="text-muted">jpg, png, webp, svg — max 2MB</small>
                                                            <img class="icon-preview mt-2" src="" alt="" style="height:48px; width:48px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:none;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="names[0]" class="form-control" placeholder="e.g. Cardiology" required>
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
                                    <a href="{{ route('home-specialities.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            var tbody   = document.getElementById('specialities-tbody');
            var addBtn  = document.getElementById('add-more-btn');

            // Start the new-row counter above the highest existing index
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
                            '<input type="text" name="names[' + idx + ']" class="form-control" placeholder="e.g. Cardiology" required>' +
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

            // Live preview of the selected icon (delegated so it works for new rows too)
            tbody.addEventListener('change', function (e) {
                if (!e.target.matches('input[type="file"].icon-input')) return;

                var file = e.target.files && e.target.files[0];
                var preview = e.target.closest('td').querySelector('.icon-preview');
                if (!preview) return;

                if (!file) {
                    // User cleared the picker — fall back to existing icon if there is one
                    var existing = e.target.closest('td').querySelector('input[name^="existing_icons"]');
                    if (existing && existing.value) {
                        preview.src = "{{ asset('home/specialities') }}/" + existing.value;
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
