@php
    $isEdit = isset($about) && $about;
    $action = $isEdit ? route('manage-about-us.update', $about->id) : route('manage-about-us.store');

    // ---- About info items (old() wins on validation error, else the stored value) ----
    if (old('about_info_heading') !== null) {
        $infoItems = [];
        foreach (old('about_info_heading') as $i => $h) {
            $infoItems[$i] = [
                'heading'     => $h,
                'description' => old('about_info_desc.'.$i),
                'image'       => old('about_info_existing.'.$i),
            ];
        }
    } else {
        $infoItems = $isEdit ? ($about->about_info_items ?? []) : [];
    }
    if (empty($infoItems)) {
        $infoItems = [['heading' => null, 'description' => null, 'image' => null]];
    }
    $infoNextIndex = max(array_keys($infoItems)) + 1;

    // ---- Commitment items ----
    if (old('commitment_title') !== null || old('commitment_count') !== null) {
        $commitItems = [];
        $keys = array_unique(array_merge(array_keys(old('commitment_title', [])), array_keys(old('commitment_count', []))));
        foreach ($keys as $i) {
            $commitItems[$i] = [
                'count' => old('commitment_count.'.$i),
                'title' => old('commitment_title.'.$i),
                'image' => old('commitment_existing.'.$i),
            ];
        }
    } else {
        $commitItems = $isEdit ? ($about->commitment_items ?? []) : [];
    }
    if (empty($commitItems)) {
        $commitItems = [['count' => null, 'title' => null, 'image' => null]];
    }
    $commitNextIndex = max(array_keys($commitItems)) + 1;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form id="about-us-form" class="row g-4 custom-input" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ============================ Banner ============================ --}}
    <div class="col-12 mt-2">
        <h5 class="mb-0 py-2 px-3 rounded" style="background:#eef1ff; border-left:4px solid #7366ff; color:#333;">Banner</h5>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="banner_heading">Banner Heading <span class="txt-danger">*</span></label>
        <input class="form-control" id="banner_heading" type="text" name="banner_heading"
            value="{{ old('banner_heading', $about->banner_heading ?? '') }}" placeholder="Enter Banner Heading">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="banner_image">Banner Image</label>
        <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
        <div class="mt-2">
            @php $bImg = $isEdit && !empty($about->banner_image) ? asset('about_us/'.$about->banner_image) : ''; @endphp
            <img id="banner_image-preview" src="{{ $bImg }}" data-existing="{{ $bImg }}" alt=""
                style="max-height:140px;max-width:220px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $bImg ? '' : 'display:none;' }}">
        </div>
    </div>

    {{-- ============================ About Section ============================ --}}
    <div class="col-12 mt-4 pt-2">
        <h5 class="mb-0 py-2 px-3 rounded" style="background:#eef1ff; border-left:4px solid #7366ff; color:#333;">About Section</h5>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="about_heading">Heading <span class="txt-danger">*</span></label>
        <input class="form-control" id="about_heading" type="text" name="about_heading"
            value="{{ old('about_heading', $about->about_heading ?? '') }}" placeholder="Enter About Heading">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="about_image">Image</label>
        <input class="form-control" id="about_image" type="file" name="about_image" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
        <div class="mt-2">
            @php $aImg = $isEdit && !empty($about->about_image) ? asset('about_us/'.$about->about_image) : ''; @endphp
            <img id="about_image-preview" src="{{ $aImg }}" data-existing="{{ $aImg }}" alt=""
                style="max-height:140px;max-width:220px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $aImg ? '' : 'display:none;' }}">
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label" for="about_description">Description <span class="txt-danger">*</span></label>
        <textarea class="form-control ck-editor" id="about_description" name="about_description" rows="4"
            placeholder="Enter About Description">{{ old('about_description', $about->about_description ?? '') }}</textarea>
    </div>

    {{-- About info repeater (image + heading + description editor) --}}
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Info Cards</label>
            <button type="button" class="btn btn-sm btn-success" id="add-about-info">+ Add More</button>
        </div>
        <div id="about-info-body">
            @foreach($infoItems as $i => $it)
                <div class="border rounded p-3 mb-3 about-info-row">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Image</label>
                            <input type="file" name="about_info_image[{{ $i }}]" accept="image/*" class="form-control repeater-image">
                            <input type="hidden" name="about_info_existing[{{ $i }}]" value="{{ $it['image'] ?? '' }}">
                            <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
                            <div class="mt-2">
                                @php $iImg = !empty($it['image']) ? asset('about_us/'.$it['image']) : ''; @endphp
                                <img class="repeater-preview" src="{{ $iImg }}" alt=""
                                    style="max-height:110px;max-width:160px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $iImg ? '' : 'display:none;' }}">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Heading</label>
                            <input type="text" name="about_info_heading[{{ $i }}]" class="form-control mb-2" value="{{ $it['heading'] ?? '' }}" placeholder="Info heading">
                            <label class="form-label">Description</label>
                            <textarea name="about_info_desc[{{ $i }}]" id="about_info_desc_{{ $i }}" class="form-control ck-editor" rows="3">{{ $it['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-sm btn-danger remove-repeater-row">Remove</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================ Our Values ============================ --}}
    <div class="col-12 mt-4 pt-2">
        <h5 class="mb-0 py-2 px-3 rounded" style="background:#eef1ff; border-left:4px solid #7366ff; color:#333;">Our Values Section</h5>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="values_heading">Heading <span class="txt-danger">*</span></label>
        <input class="form-control" id="values_heading" type="text" name="values_heading"
            value="{{ old('values_heading', $about->values_heading ?? '') }}" placeholder="Enter Values Heading">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="values_image">Image</label>
        <input class="form-control" id="values_image" type="file" name="values_image" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
        <div class="mt-2">
            @php $vImg = $isEdit && !empty($about->values_image) ? asset('about_us/'.$about->values_image) : ''; @endphp
            <img id="values_image-preview" src="{{ $vImg }}" data-existing="{{ $vImg }}" alt=""
                style="max-height:140px;max-width:220px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $vImg ? '' : 'display:none;' }}">
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label" for="values_description">Description <span class="txt-danger">*</span></label>
        <textarea class="form-control ck-editor" id="values_description" name="values_description" rows="4"
            placeholder="Enter Values Description">{{ old('values_description', $about->values_description ?? '') }}</textarea>
    </div>

    {{-- ============================ Reflecting Commitment ============================ --}}
    <div class="col-12 mt-4 pt-2">
        <h5 class="mb-0 py-2 px-3 rounded" style="background:#eef1ff; border-left:4px solid #7366ff; color:#333;">Reflecting Commitment Section</h5>
    </div>

    <div class="col-md-12">
        <label class="form-label" for="commitment_heading">Heading <span class="txt-danger">*</span></label>
        <input class="form-control" id="commitment_heading" type="text" name="commitment_heading"
            value="{{ old('commitment_heading', $about->commitment_heading ?? '') }}" placeholder="Enter Commitment Heading">
    </div>

    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Commitment Cards</label>
            <button type="button" class="btn btn-sm btn-success" id="add-commitment">+ Add More</button>
        </div>
        <div id="commitment-body">
            @foreach($commitItems as $i => $it)
                <div class="border rounded p-3 mb-3 commitment-row">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <label class="form-label">Image</label>
                            <input type="file" name="commitment_image[{{ $i }}]" accept="image/*" class="form-control repeater-image">
                            <input type="hidden" name="commitment_existing[{{ $i }}]" value="{{ $it['image'] ?? '' }}">
                            <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
                            <div class="mt-2">
                                @php $cImg = !empty($it['image']) ? asset('about_us/'.$it['image']) : ''; @endphp
                                <img class="repeater-preview" src="{{ $cImg }}" alt=""
                                    style="max-height:110px;max-width:160px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $cImg ? '' : 'display:none;' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Count</label>
                            <input type="text" name="commitment_count[{{ $i }}]" class="form-control" value="{{ $it['count'] ?? '' }}" placeholder="e.g. 5000+">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="commitment_title[{{ $i }}]" class="form-control" value="{{ $it['title'] ?? '' }}" placeholder="e.g. Pets Treated">
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-sm btn-danger remove-repeater-row">Remove</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================ Contact ============================ --}}
    <div class="col-12 mt-4 pt-2">
        <h5 class="mb-0 py-2 px-3 rounded" style="background:#eef1ff; border-left:4px solid #7366ff; color:#333;">Contact Section</h5>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="contact_image">Image</label>
        <input class="form-control" id="contact_image" type="file" name="contact_image" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>
        <div class="mt-2">
            @php $ctImg = $isEdit && !empty($about->contact_image) ? asset('about_us/'.$about->contact_image) : ''; @endphp
            <img id="contact_image-preview" src="{{ $ctImg }}" data-existing="{{ $ctImg }}" alt=""
                style="max-height:140px;max-width:220px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;{{ $ctImg ? '' : 'display:none;' }}">
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label" for="contact_description">Description <span class="txt-danger">*</span></label>
        <textarea class="form-control ck-editor" id="contact_description" name="contact_description" rows="4"
            placeholder="Enter Contact Description">{{ old('contact_description', $about->contact_description ?? '') }}</textarea>
    </div>

    <div class="col-12 text-end">
        <a href="{{ route('manage-about-us.index') }}" class="btn btn-danger px-4">Cancel</a>
        <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Update' : 'Submit' }}</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ABOUT_EDITOR_CONFIG = {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'link', 'blockQuote',
                'bulletedList', 'numberedList', '|',
                'alignment', 'outdent', 'indent', '|',
                'insertTable', 'horizontalLine', '|',
                'undo', 'redo', 'removeFormat'
            ]
        };

        window.aboutEditors = window.aboutEditors || [];

        function initEditor(el) {
            if (!window.ClassicEditor || !el || el.dataset.ckInited) return;
            el.dataset.ckInited = '1';
            ClassicEditor.create(el, ABOUT_EDITOR_CONFIG)
                .then(function (ed) { window.aboutEditors.push(ed); })
                .catch(function (e) { console.error(e); });
        }

        document.querySelectorAll('.ck-editor').forEach(initEditor);

        // ---- Single image previews ----
        function wirePreview(inputId) {
            var input   = document.getElementById(inputId);
            var preview = document.getElementById(inputId + '-preview');
            if (!input || !preview) return;
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) {
                    var existing = preview.getAttribute('data-existing');
                    preview.src = existing || '';
                    preview.style.display = existing ? 'block' : 'none';
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            });
        }
        ['banner_image', 'about_image', 'values_image', 'contact_image'].forEach(wirePreview);

        // ---- Repeater image previews (delegated) ----
        document.addEventListener('change', function (e) {
            if (!e.target.classList || !e.target.classList.contains('repeater-image')) return;
            var row = e.target.closest('.about-info-row, .commitment-row');
            if (!row) return;
            var preview = row.querySelector('.repeater-preview');
            if (!preview) return;
            var file = e.target.files && e.target.files[0];
            if (!file) { preview.src = ''; preview.style.display = 'none'; return; }
            var reader = new FileReader();
            reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(file);
        });

        // ---- About-info repeater ----
        var infoBody = document.getElementById('about-info-body');
        var infoAdd  = document.getElementById('add-about-info');
        var infoIndex = {{ $infoNextIndex }};
        if (infoAdd && infoBody) {
            infoAdd.addEventListener('click', function () {
                var i = infoIndex++;
                var div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 about-info-row';
                div.innerHTML =
                    '<div class="row g-3">' +
                        '<div class="col-md-4">' +
                            '<label class="form-label">Image</label>' +
                            '<input type="file" name="about_info_image[' + i + ']" accept="image/*" class="form-control repeater-image">' +
                            '<input type="hidden" name="about_info_existing[' + i + ']" value="">' +
                            '<small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>' +
                            '<div class="mt-2"><img class="repeater-preview" src="" alt="" style="max-height:110px;max-width:160px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;display:none;"></div>' +
                        '</div>' +
                        '<div class="col-md-8">' +
                            '<label class="form-label">Heading</label>' +
                            '<input type="text" name="about_info_heading[' + i + ']" class="form-control mb-2" placeholder="Info heading">' +
                            '<label class="form-label">Description</label>' +
                            '<textarea name="about_info_desc[' + i + ']" id="about_info_desc_' + i + '" class="form-control ck-editor" rows="3"></textarea>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-end mt-2"><button type="button" class="btn btn-sm btn-danger remove-repeater-row">Remove</button></div>';
                infoBody.appendChild(div);
                initEditor(div.querySelector('.ck-editor'));
            });
        }

        // ---- Commitment repeater ----
        var commitBody = document.getElementById('commitment-body');
        var commitAdd  = document.getElementById('add-commitment');
        var commitIndex = {{ $commitNextIndex }};
        if (commitAdd && commitBody) {
            commitAdd.addEventListener('click', function () {
                var i = commitIndex++;
                var div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 commitment-row';
                div.innerHTML =
                    '<div class="row g-3 align-items-start">' +
                        '<div class="col-md-4">' +
                            '<label class="form-label">Image</label>' +
                            '<input type="file" name="commitment_image[' + i + ']" accept="image/*" class="form-control repeater-image">' +
                            '<input type="hidden" name="commitment_existing[' + i + ']" value="">' +
                            '<small class="text-muted">jpg, jpeg, png, webp — max 2MB</small>' +
                            '<div class="mt-2"><img class="repeater-preview" src="" alt="" style="max-height:110px;max-width:160px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;display:none;"></div>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                            '<label class="form-label">Count</label>' +
                            '<input type="text" name="commitment_count[' + i + ']" class="form-control" placeholder="e.g. 5000+">' +
                        '</div>' +
                        '<div class="col-md-4">' +
                            '<label class="form-label">Title</label>' +
                            '<input type="text" name="commitment_title[' + i + ']" class="form-control" placeholder="e.g. Pets Treated">' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-end mt-2"><button type="button" class="btn btn-sm btn-danger remove-repeater-row">Remove</button></div>';
                commitBody.appendChild(div);
            });
        }

        // ---- Remove repeater rows (delegated) ----
        document.addEventListener('click', function (e) {
            if (!e.target.classList || !e.target.classList.contains('remove-repeater-row')) return;
            var row = e.target.closest('.about-info-row, .commitment-row');
            if (!row) return;
            var container = row.parentElement;

            // Destroy any CKEditor bound to this row before removing it.
            var ta = row.querySelector('.ck-editor');
            if (ta) {
                window.aboutEditors = window.aboutEditors.filter(function (ed) {
                    if (ed.sourceElement === ta) { ed.destroy(); return false; }
                    return true;
                });
            }

            var siblings = container.querySelectorAll('.about-info-row, .commitment-row');
            if (siblings.length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input, textarea').forEach(function (inp) { inp.value = ''; });
                var pv = row.querySelector('.repeater-preview');
                if (pv) { pv.src = ''; pv.style.display = 'none'; }
            }
        });

        // ---- Sync all editors back to their textareas on submit ----
        var form = document.getElementById('about-us-form');
        if (form) {
            form.addEventListener('submit', function () {
                (window.aboutEditors || []).forEach(function (ed) {
                    try { ed.updateSourceElement(); } catch (err) {}
                });
            });
        }
    });
</script>
