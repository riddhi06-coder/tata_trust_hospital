<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
    <style>
        .section-heading {
            background: linear-gradient(90deg, #eef2ff 0%, #f8fafc 100%);
            padding: 10px 16px;
            margin: -1rem -1rem 1rem -1rem;
            border-bottom: 2px solid #6366f1;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        .section-heading h5 { margin: 0 !important; font-weight: 700; color: #1e3a8a; font-size: 1.05rem; }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Add Contact Details</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-contact-details.index') }}">Contact Details</a></li>
                            <li class="breadcrumb-item active">Add Contact Details</li>
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
                            <h4>Contact Details Form</h4>
                            <p class="f-m-light mt-1">Fill in the details below.</p>
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

                            <form class="row g-4 custom-input" action="{{ route('manage-contact-details.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- ===================== BANNER ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Banner</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="banner_heading">Banner Heading <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="banner_heading" type="text" name="banner_heading"
                                                    value="{{ old('banner_heading') }}" placeholder="Enter Banner Heading">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="banner_image">Banner Image <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
                                                <small class="text-muted">jpg, jpeg, png, webp — max 10MB</small>
                                                <div class="mt-2">
                                                    <img id="banner_image-preview" src="" alt=""
                                                        style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; display:none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== ADDRESS & CONTACTS ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Address &amp; Contact</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="address">Address <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="address" name="address" rows="3"
                                                    placeholder="Full address (street, city, state, postal code)">{{ old('address') }}</textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="email">Primary Email <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="email" type="email" name="email"
                                                    value="{{ old('email') }}" placeholder="info@example.com">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="footer_email">Footer Email</label>
                                                <input class="form-control" id="footer_email" type="email" name="footer_email"
                                                    value="{{ old('footer_email') }}" placeholder="contact@example.com">
                                                <small class="text-muted">Shown in the site footer if different from the primary email.</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="emergency_no">Emergency No. <span class="txt-danger">*</span></label>
                                                <input class="form-control" id="emergency_no" type="text" name="emergency_no"
                                                    value="{{ old('emergency_no') }}" placeholder="+91 98765 43210">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="join_team_email">Join Team Email</label>
                                                <input class="form-control" id="join_team_email" type="email" name="join_team_email"
                                                    value="{{ old('join_team_email') }}" placeholder="careers@example.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== HEADER RIBBON ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading d-flex justify-content-between align-items-center">
                                            <h5>Header Ribbon Details</h5>
                                            <button type="button" class="btn btn-primary btn-sm" id="ribbon-add-btn">+ Add More</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle" id="ribbon-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 22%;">Icon</th>
                                                        <th style="width: 30%;">Title <span class="text-danger">*</span></th>
                                                        <th>Value</th>
                                                        <th style="width: 90px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ribbon-tbody">
                                                    <tr data-row="0">
                                                        <td>
                                                            <input type="file" name="ribbon[0][icon]" accept="image/*" class="form-control ribbon-icon-input">
                                                            <small class="text-muted">jpg, png, webp, svg — max 2MB</small>
                                                            <img class="ribbon-icon-preview mt-2" src="" alt=""
                                                                style="height:40px; width:40px; object-fit:contain; border-radius:4px; border:1px solid #e5e7eb; background:#1e3a8a; padding:4px; display:none;">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="ribbon[0][title]" class="form-control" placeholder="e.g. Emergency, Book Appointment">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="ribbon[0][value]" class="form-control" placeholder="Phone, email, link, or short text">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-ribbon-row" disabled title="At least one row required">Remove</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== SOCIAL MEDIA LINKS ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading d-flex justify-content-between align-items-center">
                                            <h5>Social Media Links</h5>
                                            <button type="button" class="btn btn-primary btn-sm" id="social-add-btn">+ Add More</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle" id="social-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 30%;">Platform <span class="text-danger">*</span></th>
                                                        <th>URL <span class="text-danger">*</span></th>
                                                        <th style="width: 90px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="social-tbody">
                                                    <tr data-row="0">
                                                        <td>
                                                            <select name="social[0][platform]" class="form-select">
                                                                <option value="">— Select Platform —</option>
                                                                @foreach($platforms as $key => $p)
                                                                    <option value="{{ $key }}">{{ $p['label'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="url" name="social[0][url]" class="form-control" placeholder="https://...">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-social-row" disabled title="At least one row required">Remove</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== DONATE INFO ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Donate Info</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="donate_info">Donate Info</label>
                                                <textarea class="form-control ckeditor-init" id="donate_info" name="donate_info" rows="6"
                                                    placeholder="Donation-related information (bank details, links, etc.)">{{ old('donate_info') }}</textarea>
                                                <small class="text-muted">Supports rich text (headings, lists, links).</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== MAP ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Map</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="map_url">Map URL</label>
                                                <input class="form-control" id="map_url" type="url" name="map_url"
                                                    value="{{ old('map_url') }}" placeholder="https://maps.google.com/?q=...">
                                                <small class="text-muted">Direct Google Maps link — used for the "Get Directions" button.</small>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="iframe_url">Iframe URL</label>
                                                <textarea class="form-control" id="iframe_url" name="iframe_url" rows="3"
                                                    placeholder='https://www.google.com/maps/embed?pb=...  OR  paste the whole <iframe ...> code'>{{ old('iframe_url') }}</textarea>
                                                <small class="text-muted">Paste either the embed URL or the full <code>&lt;iframe&gt;</code> HTML from Google Maps → Share → Embed.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== ACTIONS ===================== --}}
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-contact-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Submit</button>
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
            /* ---------- CKEditor: attach to every .ckeditor-init textarea ---------- */
            var editorInstances = new Map();
            function initCKEditor(textarea) {
                if (!textarea || editorInstances.has(textarea) || typeof ClassicEditor === 'undefined') return;
                textarea.removeAttribute('required');
                ClassicEditor.create(textarea, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'link',
                        'bulletedList', 'numberedList', '|',
                        'alignment', 'outdent', 'indent', '|',
                        'undo', 'redo', 'removeFormat'
                    ]
                }).then(function (editor) { editorInstances.set(textarea, editor); })
                  .catch(function (err) { console.error(err); });
            }
            function initAllEditors() {
                document.querySelectorAll('.ckeditor-init').forEach(initCKEditor);
            }
            if (typeof ClassicEditor === 'undefined') {
                var tries = 0;
                var waitCk = setInterval(function () {
                    if (typeof ClassicEditor !== 'undefined' || tries++ > 40) {
                        clearInterval(waitCk);
                        initAllEditors();
                    }
                }, 100);
            } else {
                initAllEditors();
            }

            /* ---------- Banner image preview ---------- */
            (function () {
                var input = document.getElementById('banner_image');
                var preview = document.getElementById('banner_image-preview');
                if (!input || !preview) return;
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) { preview.src = ''; preview.style.display = 'none'; return; }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                });
            })();

            /* ---------- Ribbon table: add / remove rows ---------- */
            var tbody  = document.getElementById('ribbon-tbody');
            var addBtn = document.getElementById('ribbon-add-btn');
            var nextIdx = 1;

            addBtn.addEventListener('click', function () {
                var idx = nextIdx++;
                var tr = document.createElement('tr');
                tr.setAttribute('data-row', idx);
                tr.innerHTML =
                    '<td>' +
                        '<input type="file" name="ribbon[' + idx + '][icon]" accept="image/*" class="form-control ribbon-icon-input">' +
                        '<small class="text-muted">jpg, png, webp, svg &mdash; max 2MB</small>' +
                        '<img class="ribbon-icon-preview mt-2" src="" alt="" style="height:40px; width:40px; object-fit:contain; border-radius:4px; border:1px solid #e5e7eb; background:#1e3a8a; padding:4px; display:none;">' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" name="ribbon[' + idx + '][title]" class="form-control" placeholder="e.g. Emergency, Book Appointment">' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" name="ribbon[' + idx + '][value]" class="form-control" placeholder="Phone, email, link, or short text">' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm remove-ribbon-row">Remove</button>' +
                    '</td>';
                tbody.appendChild(tr);
            });

            tbody.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-ribbon-row')) return;
                if (e.target.disabled) return;
                e.target.closest('tr').remove();
            });

            tbody.addEventListener('change', function (e) {
                if (!e.target.matches('input[type="file"].ribbon-icon-input')) return;
                var file = e.target.files && e.target.files[0];
                var preview = e.target.closest('td').querySelector('.ribbon-icon-preview');
                if (!preview) return;
                if (!file) { preview.src = ''; preview.style.display = 'none'; return; }
                var reader = new FileReader();
                reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            });

            /* ---------- Social table: add / remove rows ---------- */
            var socialTbody  = document.getElementById('social-tbody');
            var socialAddBtn = document.getElementById('social-add-btn');
            var socialNextIdx = 1;
            var platformOptions = @json($platforms);

            function buildPlatformOptions() {
                var html = '<option value="">— Select Platform —</option>';
                Object.keys(platformOptions).forEach(function (key) {
                    html += '<option value="' + key + '">' + platformOptions[key].label + '</option>';
                });
                return html;
            }

            socialAddBtn.addEventListener('click', function () {
                var idx = socialNextIdx++;
                var tr = document.createElement('tr');
                tr.setAttribute('data-row', idx);
                tr.innerHTML =
                    '<td>' +
                        '<select name="social[' + idx + '][platform]" class="form-select">' + buildPlatformOptions() + '</select>' +
                    '</td>' +
                    '<td>' +
                        '<input type="url" name="social[' + idx + '][url]" class="form-control" placeholder="https://...">' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm remove-social-row">Remove</button>' +
                    '</td>';
                socialTbody.appendChild(tr);
            });

            socialTbody.addEventListener('click', function (e) {
                if (!e.target.classList.contains('remove-social-row')) return;
                if (e.target.disabled) return;
                e.target.closest('tr').remove();
            });
        });
    </script>

</body>

</html>
