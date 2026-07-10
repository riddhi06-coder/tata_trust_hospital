<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php $imageUrl = $detail->image ? asset('home/blog/details/'.$detail->image) : ''; @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Blog Detail</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-blog-details.index') }}">Blog Details</a></li>
                            <li class="breadcrumb-item active">Edit Blog Detail</li>
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
                            <h4>Update Blog Detail</h4>
                            <p class="f-m-light mt-1">Update the detail content below.</p>
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

                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form class="row g-4 custom-input" action="{{ route('manage-blog-details.update', $detail->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- ===================== BLOG DETAIL ===================== --}}
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="section-heading"><h5>Blog Detail</h5></div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="blog_listing_id">Blog Title <span class="txt-danger">*</span></label>
                                                <select class="form-select" id="blog_listing_id" name="blog_listing_id">
                                                    <option value="">— Select Blog —</option>
                                                    @foreach($listings as $listing)
                                                        <option value="{{ $listing->id }}" {{ (string) old('blog_listing_id', $detail->blog_listing_id) === (string) $listing->id ? 'selected' : '' }}>
                                                            {{ $listing->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="image">Image</label>
                                                <input class="form-control" id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                                                <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 10MB.</small>
                                                <div class="mt-2">
                                                    <img id="image-preview"
                                                        src="{{ $imageUrl }}"
                                                        data-existing="{{ $imageUrl }}"
                                                        alt=""
                                                        style="max-height:200px; max-width:320px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $imageUrl ? '' : 'display:none;' }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="information">Information <span class="txt-danger">*</span></label>
                                                <textarea class="form-control ckeditor-init" id="information" name="information" rows="10"
                                                    placeholder="Full blog content...">{{ old('information', $detail->information) }}</textarea>
                                            </div>
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
                                                        <th style="width: 30%;">Platform</th>
                                                        <th>URL</th>
                                                        <th style="width: 90px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="social-tbody">
                                                    @forelse($socials as $i => $row)
                                                        <tr data-row="{{ $i }}">
                                                            <td>
                                                                <input type="hidden" name="social[{{ $i }}][id]" value="{{ $row->id }}">
                                                                <select name="social[{{ $i }}][platform]" class="form-select">
                                                                    <option value="">— Select Platform —</option>
                                                                    @foreach($platforms as $key => $p)
                                                                        <option value="{{ $key }}" {{ old('social.'.$i.'.platform', $row->platform) === $key ? 'selected' : '' }}>{{ $p['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="url" name="social[{{ $i }}][url]" class="form-control"
                                                                    value="{{ old('social.'.$i.'.url', $row->url) }}" placeholder="https://...">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-social-row">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @empty
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-social-row" disabled>Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== ACTIONS ===================== --}}
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-blog-details.index') }}" class="btn btn-danger px-4">Cancel</a>
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

            /* ---------- CKEditor multi-init ---------- */
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
            function initAllEditors() { document.querySelectorAll('.ckeditor-init').forEach(initCKEditor); }
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

            /* ---------- Image preview with keep-existing ---------- */
            (function () {
                var input = document.getElementById('image');
                var preview = document.getElementById('image-preview');
                if (!input || !preview) return;
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        var existing = preview.getAttribute('data-existing');
                        if (existing) { preview.src = existing; preview.style.display = 'block'; }
                        else { preview.src = ''; preview.style.display = 'none'; }
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(file);
                });
            })();

            /* ---------- Social table ---------- */
            var socialTbody  = document.getElementById('social-tbody');
            var socialAddBtn = document.getElementById('social-add-btn');
            var socialNextIdx = socialTbody.querySelectorAll('tr').length;
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
                if (!confirm('Remove this row?')) return;
                e.target.closest('tr').remove();
            });
        });
    </script>

</body>

</html>
