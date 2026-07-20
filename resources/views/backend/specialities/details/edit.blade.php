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
                    <div class="col-6"><h4>Edit Speciality Details</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('speciality-details.index') }}">Speciality Details</a></li>
                            <li class="breadcrumb-item active">Edit Speciality Details</li>
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
                            <h4>Update Speciality Details</h4>
                            <p class="f-m-light mt-1">Existing images are kept unless you upload new ones.</p>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="row g-4 custom-input spec-details-form" action="{{ route('speciality-details.update', $detail->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label" for="speciality_id">Speciality <span class="txt-danger">*</span></label>
                                    <select class="form-control" id="speciality_id" name="speciality_id" required>
                                        <option value="">— Select Speciality —</option>
                                        @foreach($specialities as $spec)
                                            <option value="{{ $spec->id }}"
                                                data-preventive="{{ \Illuminate\Support\Str::contains(strtolower($spec->speciality.' '.$spec->slug), 'preventive') ? '1' : '0' }}"
                                                {{ (string) old('speciality_id', $detail->speciality_id) === (string) $spec->id ? 'selected' : '' }}>
                                                {{ $spec->speciality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Banner image is shown in BOTH layouts --}}
                                <div class="col-md-6">
                                    <label class="form-label" for="banner_image">Banner Image</label>
                                    <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="banner_image-preview"
                                            src="{{ $detail->banner_image ? asset('home/speciality-details/'.$detail->banner_image) : '' }}"
                                            data-existing="{{ $detail->banner_image ? asset('home/speciality-details/'.$detail->banner_image) : '' }}"
                                            alt=""
                                            style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $detail->banner_image ? '' : 'display:none;' }}">
                                    </div>
                                </div>

                                {{-- Section Image / Heading / Description are shown in BOTH layouts (like the banner) --}}
                                <div class="col-md-6">
                                    <label class="form-label" for="section_image">Section Image</label>
                                    <input class="form-control" id="section_image" type="file" name="section_image" accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="section_image-preview"
                                            src="{{ $detail->section_image ? asset('home/speciality-details/'.$detail->section_image) : '' }}"
                                            data-existing="{{ $detail->section_image ? asset('home/speciality-details/'.$detail->section_image) : '' }}"
                                            alt=""
                                            style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $detail->section_image ? '' : 'display:none;' }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="section_heading">Section Heading</label>
                                    <input class="form-control" id="section_heading" type="text" name="section_heading"
                                        value="{{ old('section_heading', $detail->section_heading) }}" placeholder="Enter Section Heading">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="editor">Section Description</label>
                                    <textarea class="form-control" id="editor" name="section_description" rows="6"
                                        placeholder="Enter Section Description">{{ old('section_description', $detail->section_description) }}</textarea>
                                </div>

                                {{-- ================= NORMAL layout ================= --}}
                                <div class="col-12 normal-fields">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label" for="service_heading">Service Heading</label>
                                            <input class="form-control" id="service_heading" type="text" name="service_heading"
                                                value="{{ old('service_heading', $detail->service_heading) }}" placeholder="Enter Service Heading">
                                        </div>

                                        <div class="col-md-12">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Services</label>
                                                <button type="button" class="btn btn-sm btn-add-row" id="add-service">+ Add More</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-0" id="services-table">
                                                    <thead class="table-light">
                                                        <tr><th style="width:60px;">#</th><th>Description</th><th style="width:110px;" class="text-end">Action</th></tr>
                                                    </thead>
                                                    <tbody id="services-body">
                                                        @php
                                                            $servicesList = old('services', $detail->services ?? ['']);
                                                            if (!is_array($servicesList) || count($servicesList) === 0) { $servicesList = ['']; }
                                                        @endphp
                                                        @foreach($servicesList as $idx => $svc)
                                                            <tr class="service-row">
                                                                <td class="row-index">{{ $idx + 1 }}</td>
                                                                <td><textarea class="form-control" name="services[]" rows="2" placeholder="Enter service description">{{ $svc }}</textarea></td>
                                                                <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-service">Remove</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="short_info">Short Info</label>
                                            <textarea class="form-control" id="short_info" name="short_info" rows="3"
                                                placeholder="Enter Short Info (optional)">{{ old('short_info', $detail->short_info) }}</textarea>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Attached Doctors</label>
                                                <button type="button" class="btn btn-sm btn-add-row" id="add-doctor">+ Add Doctor</button>
                                            </div>
                                            <small class="text-muted d-block mb-2">Pick doctors from Our Team to feature on this speciality page. Optionally override their bio for this context.</small>
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-0" id="doctors-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:60px;">#</th>
                                                            <th style="width:32%;">Doctor</th>
                                                            <th>Bio Override <small class="text-muted">(optional)</small></th>
                                                            <th style="width:110px;" class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="doctors-body">
                                                        @php
                                                            $oldDoctorIds  = old('doctor_ids');
                                                            $oldDoctorBios = old('doctor_bio_override', []);
                                                            if (!is_array($oldDoctorIds)) {
                                                                $oldDoctorIds  = $detail->doctors->pluck('id')->all();
                                                                $oldDoctorBios = $detail->doctors->map(fn ($d) => $d->pivot->bio_override)->all();
                                                            }
                                                        @endphp
                                                        @foreach($oldDoctorIds as $rIdx => $rDocId)
                                                            <tr class="doctor-row">
                                                                <td class="row-index">{{ $rIdx + 1 }}</td>
                                                                <td>
                                                                    <select class="form-control doctor-select" name="doctor_ids[]">
                                                                        <option value="">— Select Doctor —</option>
                                                                        @foreach($doctors as $doc)
                                                                            <option value="{{ $doc->id }}" {{ (string) $rDocId === (string) $doc->id ? 'selected' : '' }}>
                                                                                {{ $doc->name }}{{ $doc->designation ? ' — '.$doc->designation : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><textarea class="form-control" name="doctor_bio_override[]" rows="2" placeholder="Leave blank to use the doctor's master bio">{{ $oldDoctorBios[$rIdx] ?? '' }}</textarea></td>
                                                                <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-doctor">Remove</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= PREVENTIVE layout ================= --}}
                                @php
                                    // Services included rows (old() on validation error, else saved).
                                    if (old('preventive_service_name') !== null) {
                                        $svcNames = (array) old('preventive_service_name', []);
                                        $svcExist = (array) old('preventive_service_existing', []);
                                        $pservices = [];
                                        foreach ($svcNames as $i => $nm) { $pservices[] = ['name' => $nm, 'image' => $svcExist[$i] ?? null]; }
                                    } else {
                                        $pservices = $detail->preventive_services ?? [];
                                    }
                                    if (empty($pservices)) { $pservices = [['name' => '', 'image' => null]]; }

                                    if (old('plan_category') !== null) {
                                        $planBlocks = [];
                                        foreach ((array) old('plan_category', []) as $b => $catName) {
                                            $bNames  = (array) old('plan_name.'.$b, []);
                                            $bRanges = (array) old('plan_age_range.'.$b, []);
                                            $bCosts  = (array) old('plan_cost.'.$b, []);
                                            $bExist  = (array) old('plan_existing.'.$b, []);
                                            $rows = [];
                                            foreach ($bNames as $r => $nm) {
                                                $rows[] = ['name' => $nm, 'age_range' => $bRanges[$r] ?? '', 'cost' => $bCosts[$r] ?? '', 'image' => $bExist[$r] ?? null];
                                            }
                                            if (empty($rows)) { $rows = [['name' => '', 'age_range' => '', 'cost' => '', 'image' => null]]; }
                                            $planBlocks[$b] = ['category' => $catName, 'plans' => $rows];
                                        }
                                    } else {
                                        $planBlocks = $detail->preventive_plans ?? [];
                                    }
                                    if (empty($planBlocks)) { $planBlocks = [['category' => '', 'plans' => [['name' => '', 'age_range' => '', 'cost' => '', 'image' => null]]]]; }
                                @endphp
                                <div class="col-12 preventive-fields" style="display:none;">
                                    <div class="row g-4">

                                        <div class="col-12"><hr class="my-1"><h5 class="mb-0">Preventive</h5></div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="preventive_section_heading">Section Heading</label>
                                            <input class="form-control" type="text" id="preventive_section_heading" name="preventive_section_heading"
                                                value="{{ old('preventive_section_heading', $detail->preventive_section_heading) }}" placeholder="Enter Section Heading">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="preventive_section_editor">Section Description</label>
                                            <textarea class="form-control preventive-editor" id="preventive_section_editor" name="preventive_section_description" rows="5">{{ old('preventive_section_description', $detail->preventive_section_description) }}</textarea>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Services Included</label>
                                                <button type="button" class="btn btn-sm btn-add-row" id="add-pservice">+ Add More</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-0" id="pservice-table">
                                                    <thead class="table-light">
                                                        <tr><th style="width:60px;">#</th><th style="width:220px;">Image</th><th>Service Name</th><th style="width:110px;" class="text-end">Action</th></tr>
                                                    </thead>
                                                    <tbody id="pservice-body">
                                                        @foreach($pservices as $i => $s)
                                                            <tr class="pservice-row">
                                                                <td class="row-index">{{ $i + 1 }}</td>
                                                                <td>
                                                                    <input type="file" class="form-control row-image" name="preventive_service_image[]" accept=".jpg,.jpeg,.png,.webp">
                                                                    <input type="hidden" name="preventive_service_existing[]" value="{{ $s['image'] ?? '' }}">
                                                                    <small class="text-muted d-block mt-1">jpg, jpeg, png, webp — max 10MB</small>
                                                                    @if(!empty($s['image']))
                                                                        <img class="mt-2" src="{{ asset('home/speciality-details/'.$s['image']) }}" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;">
                                                                    @endif
                                                                    <img class="row-image-preview mt-2" src="" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;display:none;">
                                                                </td>
                                                                <td><input type="text" class="form-control" name="preventive_service_name[]" value="{{ $s['name'] ?? '' }}" placeholder="Enter service name"></td>
                                                                <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-pservice">Remove</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-12"><hr class="my-1"><h5 class="mb-0">Preventive Care Plans</h5></div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="preventive_plans_heading">Heading</label>
                                            <input class="form-control" type="text" id="preventive_plans_heading" name="preventive_plans_heading"
                                                value="{{ old('preventive_plans_heading', $detail->preventive_plans_heading) }}" placeholder="Enter Heading">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="preventive_plans_editor">Description</label>
                                            <textarea class="form-control preventive-editor" id="preventive_plans_editor" name="preventive_plans_description" rows="5">{{ old('preventive_plans_description', $detail->preventive_plans_description) }}</textarea>
                                        </div>

                                        {{-- Plans grouped by category. "Add More" duplicates the whole
                                             category block (category name + its plans table). --}}
                                        <div class="col-md-12">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Plans by Category</label>
                                                <button type="button" class="btn btn-sm btn-add-row" id="add-category">+ Add More</button>
                                            </div>
                                            <div id="plan-categories">
                                                @foreach($planBlocks as $b => $block)
                                                    <div class="plan-category-block border rounded p-3 mb-3" data-block="{{ $b }}">
                                                        <div class="row g-3 align-items-end mb-3">
                                                            <div class="col-md-12">
                                                                <label class="form-label">Category Name</label>
                                                                <input type="text" class="form-control" name="plan_category[{{ $b }}]" value="{{ $block['category'] ?? '' }}" placeholder="Enter Category Name">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="form-label mb-0">Plans</label>
                                                            <button type="button" class="btn btn-sm btn-add-row add-plan-row">+ Add Plan</button>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered align-middle mb-0 plan-table">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width:50px;">#</th><th style="width:200px;">Image</th><th>Name</th>
                                                                        <th style="width:160px;">Age Range</th><th style="width:140px;">Cost</th><th style="width:100px;" class="text-end">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="plan-rows">
                                                                    @php $rows = $block['plans'] ?? []; if (empty($rows)) { $rows = [['name'=>'','age_range'=>'','cost'=>'','image'=>null]]; } @endphp
                                                                    @foreach($rows as $p)
                                                                        <tr class="plan-row">
                                                                            <td class="row-index">{{ $loop->iteration }}</td>
                                                                            <td>
                                                                                <input type="file" class="form-control row-image" name="plan_image[{{ $b }}][]" accept=".jpg,.jpeg,.png,.webp">
                                                                                <input type="hidden" name="plan_existing[{{ $b }}][]" value="{{ $p['image'] ?? '' }}">
                                                                                <small class="text-muted d-block mt-1">jpg, jpeg, png, webp — max 10MB</small>
                                                                                @if(!empty($p['image']))
                                                                                    <img class="mt-2" src="{{ asset('home/speciality-details/'.$p['image']) }}" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;">
                                                                                @endif
                                                                                <img class="row-image-preview mt-2" src="" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;display:none;">
                                                                            </td>
                                                                            <td><input type="text" class="form-control" name="plan_name[{{ $b }}][]" value="{{ $p['name'] ?? '' }}" placeholder="Plan name"></td>
                                                                            <td><input type="text" class="form-control" name="plan_age_range[{{ $b }}][]" value="{{ $p['age_range'] ?? '' }}" placeholder="e.g. 0-1 yr"></td>
                                                                            <td><input type="text" class="form-control" name="plan_cost[{{ $b }}][]" value="{{ $p['cost'] ?? '' }}" placeholder="e.g. ₹2000"></td>
                                                                            <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-plan">Remove</button></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label" for="preventive_disclaimer_editor">Disclaimer</label>
                                            <textarea class="form-control preventive-editor" id="preventive_disclaimer_editor" name="preventive_disclaimer" rows="4">{{ old('preventive_disclaimer', $detail->preventive_disclaimer) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <a href="{{ route('speciality-details.index') }}" class="btn btn-danger px-4">Cancel</a>
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
    @include('components.backend.main-js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function wirePreview(inputId, previewId) {
                var input = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        var ex = preview.getAttribute('data-existing') || '';
                        preview.src = ex; preview.style.display = ex ? 'block' : 'none';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                });
            }
            wirePreview('banner_image', 'banner_image-preview');
            wirePreview('section_image', 'section_image-preview');

            document.addEventListener('change', function (e) {
                if (!e.target.classList || !e.target.classList.contains('row-image')) return;
                var img = e.target.parentNode.querySelector('.row-image-preview');
                if (!img) return;
                var file = e.target.files && e.target.files[0];
                if (!file) { img.src = ''; img.style.display = 'none'; return; }
                var reader = new FileReader();
                reader.onload = function (ev) { img.src = ev.target.result; img.style.display = 'block'; };
                reader.readAsDataURL(file);
            });

            function repeater(bodyId, addBtnId, rowClass, removeClass, rowHtml, minOne) {
                var body = document.getElementById(bodyId);
                var addBtn = document.getElementById(addBtnId);
                if (!body || !addBtn) return;
                function renumber() {
                    body.querySelectorAll('.' + rowClass).forEach(function (row, i) {
                        var idx = row.querySelector('.row-index'); if (idx) idx.textContent = i + 1;
                    });
                }
                addBtn.addEventListener('click', function () {
                    var tr = document.createElement('tr'); tr.className = rowClass; tr.innerHTML = rowHtml;
                    body.appendChild(tr); renumber();
                });
                body.addEventListener('click', function (e) {
                    if (!e.target.classList.contains(removeClass)) return;
                    var rows = body.querySelectorAll('.' + rowClass);
                    if (minOne && rows.length <= 1) { alert('At least one row is required.'); return; }
                    e.target.closest('.' + rowClass).remove(); renumber();
                });
            }

            repeater('services-body', 'add-service', 'service-row', 'remove-service',
                '<td class="row-index"></td>' +
                '<td><textarea class="form-control" name="services[]" rows="2" placeholder="Enter service description"></textarea></td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-service">Remove</button></td>', true);

            var doctorsList = @json($doctors->map(fn ($d) => ['id' => $d->id, 'label' => $d->name . ($d->designation ? ' — ' . $d->designation : '')])->values());
            function doctorOptions() {
                var html = '<option value="">— Select Doctor —</option>';
                doctorsList.forEach(function (d) {
                    var label = String(d.label).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    html += '<option value="' + d.id + '">' + label + '</option>';
                });
                return html;
            }
            repeater('doctors-body', 'add-doctor', 'doctor-row', 'remove-doctor',
                '<td class="row-index"></td>' +
                '<td><select class="form-control doctor-select" name="doctor_ids[]">' + doctorOptions() + '</select></td>' +
                '<td><textarea class="form-control" name="doctor_bio_override[]" rows="2" placeholder="Leave blank to use the doctor\'s master bio"></textarea></td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-doctor">Remove</button></td>', false);

            repeater('pservice-body', 'add-pservice', 'pservice-row', 'remove-pservice',
                '<td class="row-index"></td>' +
                '<td><input type="file" class="form-control row-image" name="preventive_service_image[]" accept=".jpg,.jpeg,.png,.webp">' +
                '<input type="hidden" name="preventive_service_existing[]" value="">' +
                '<small class="text-muted d-block mt-1">jpg, jpeg, png, webp — max 10MB</small>' +
                '<img class="row-image-preview mt-2" src="" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;display:none;"></td>' +
                '<td><input type="text" class="form-control" name="preventive_service_name[]" placeholder="Enter service name"></td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-pservice">Remove</button></td>', true);

            /* ---------- preventive: plans grouped by category (nested repeater) ---------- */
            var planCategories = document.getElementById('plan-categories');
            var blockCounter = {{ (is_array($planBlocks) && count($planBlocks)) ? (max(array_keys($planBlocks)) + 1) : 1 }};

            function planRowHtml(b) {
                return '<td class="row-index"></td>' +
                    '<td><input type="file" class="form-control row-image" name="plan_image[' + b + '][]" accept=".jpg,.jpeg,.png,.webp">' +
                    '<input type="hidden" name="plan_existing[' + b + '][]" value="">' +
                    '<small class="text-muted d-block mt-1">jpg, jpeg, png, webp — max 10MB</small>' +
                    '<img class="row-image-preview mt-2" src="" alt="" style="max-height:70px;border-radius:6px;border:1px solid #e5e7eb;display:none;"></td>' +
                    '<td><input type="text" class="form-control" name="plan_name[' + b + '][]" placeholder="Plan name"></td>' +
                    '<td><input type="text" class="form-control" name="plan_age_range[' + b + '][]" placeholder="e.g. 0-1 yr"></td>' +
                    '<td><input type="text" class="form-control" name="plan_cost[' + b + '][]" placeholder="e.g. ₹2000"></td>' +
                    '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-plan">Remove</button></td>';
            }

            function categoryBlockHtml(b) {
                return '<div class="plan-category-block border rounded p-3 mb-3" data-block="' + b + '">' +
                    '<div class="row g-3 align-items-end mb-3">' +
                        '<div class="col-md-12"><label class="form-label">Category Name</label>' +
                        '<input type="text" class="form-control" name="plan_category[' + b + ']" placeholder="Enter Category Name"></div>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center mb-2">' +
                        '<label class="form-label mb-0">Plans</label>' +
                        '<button type="button" class="btn btn-sm btn-add-row add-plan-row">+ Add Plan</button>' +
                    '</div>' +
                    '<div class="table-responsive"><table class="table table-bordered align-middle mb-0 plan-table">' +
                        '<thead class="table-light"><tr><th style="width:50px;">#</th><th style="width:200px;">Image</th><th>Name</th><th style="width:160px;">Age Range</th><th style="width:140px;">Cost</th><th style="width:100px;" class="text-end">Action</th></tr></thead>' +
                        '<tbody class="plan-rows"><tr class="plan-row">' + planRowHtml(b) + '</tr></tbody>' +
                    '</table></div>' +
                '</div>';
            }

            function renumberPlanRows(tbody) {
                tbody.querySelectorAll('.plan-row').forEach(function (row, i) {
                    var idx = row.querySelector('.row-index'); if (idx) idx.textContent = i + 1;
                });
            }

            document.getElementById('add-category').addEventListener('click', function () {
                var b = blockCounter++;
                var wrap = document.createElement('div');
                wrap.innerHTML = categoryBlockHtml(b);
                var block = wrap.firstChild;
                planCategories.appendChild(block);
                renumberPlanRows(block.querySelector('.plan-rows'));
            });

            planCategories.addEventListener('click', function (e) {
                if (e.target.classList.contains('add-plan-row')) {
                    var block = e.target.closest('.plan-category-block');
                    var tbody = block.querySelector('.plan-rows');
                    var tr = document.createElement('tr');
                    tr.className = 'plan-row';
                    tr.innerHTML = planRowHtml(block.getAttribute('data-block'));
                    tbody.appendChild(tr);
                    renumberPlanRows(tbody);
                } else if (e.target.classList.contains('remove-plan')) {
                    var tb = e.target.closest('.plan-rows');
                    if (tb.querySelectorAll('.plan-row').length <= 1) { alert('At least one plan is required.'); return; }
                    e.target.closest('.plan-row').remove();
                    renumberPlanRows(tb);
                } else if (e.target.classList.contains('remove-category')) {
                    if (planCategories.querySelectorAll('.plan-category-block').length <= 1) { alert('At least one category is required.'); return; }
                    e.target.closest('.plan-category-block').remove();
                }
            });

            var preventiveEditorsReady = false;
            function initPreventiveEditors() {
                if (preventiveEditorsReady || typeof ClassicEditor === 'undefined') return;
                preventiveEditorsReady = true;
                document.querySelectorAll('.preventive-editor').forEach(function (el) {
                    ClassicEditor.create(el).catch(function (err) { console.error(err); });
                });
            }

            var specSelect = document.getElementById('speciality_id');
            var normalWrap = document.querySelector('.normal-fields');
            var preventiveWrap = document.querySelector('.preventive-fields');
            function applyMode() {
                var opt = specSelect.options[specSelect.selectedIndex];
                var isPreventive = opt && opt.getAttribute('data-preventive') === '1';
                normalWrap.style.display = isPreventive ? 'none' : '';
                preventiveWrap.style.display = isPreventive ? '' : 'none';
                if (isPreventive) initPreventiveEditors();
            }
            specSelect.addEventListener('change', applyMode);
            applyMode();
        });
    </script>
</body>
</html>
