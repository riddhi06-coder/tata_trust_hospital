<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
</head>
<body>

    @include('components.backend.header')

    <!--start sidebar wrapper-->
    @include('components.backend.sidebar')
    <!--end sidebar wrapper-->


    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Team Member</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-our-team.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Team Member</li>
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
                            <h4>Team Member Form</h4>
                            @if($showBanner)
                                <p class="f-m-light mt-1">This is the first entry — page banner, section, motto and board details are edited here. Existing images are kept unless replaced.</p>
                            @else
                                <p class="f-m-light mt-1">Update the details below. Existing image is kept unless you upload a new one.</p>
                            @endif
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

                            @php $hasImage = !empty($member->image); @endphp

                            <form class="row g-3 custom-input" action="{{ route('manage-our-team.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                @if($showBanner)
                                    @php
                                        $bannerImg = $settings && !empty($settings->banner_image) ? asset('our-team/'.$settings->banner_image) : '';
                                        $mottoImg  = $settings && !empty($settings->motto_image)  ? asset('our-team/'.$settings->motto_image)  : '';
                                        $boardImg  = $settings && !empty($settings->board_image)  ? asset('our-team/'.$settings->board_image)  : '';
                                    @endphp

                                    {{-- ============ Banner ============ --}}
                                    <div class="col-12"><h5 class="mb-0">Banner</h5></div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="banner_heading">Banner Heading</label>
                                        <input class="form-control" id="banner_heading" type="text" name="banner_heading"
                                            value="{{ old('banner_heading', $settings->banner_heading ?? '') }}" placeholder="Enter Banner Heading">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="banner_image">Banner Image</label>
                                        <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 2MB.</small>
                                        <div class="mt-2">
                                            <img id="banner_image-preview" src="{{ $bannerImg }}" data-existing="{{ $bannerImg }}" alt=""
                                                style="max-height:140px; max-width:220px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $bannerImg ? '' : 'display:none;' }}">
                                        </div>
                                    </div>

                                    {{-- ============ Section ============ --}}
                                    <div class="col-12"><hr><h5 class="mb-0">Section</h5></div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="section_heading">Section Heading</label>
                                        <input class="form-control" id="section_heading" type="text" name="section_heading"
                                            value="{{ old('section_heading', $settings->section_heading ?? '') }}" placeholder="Enter Section Heading">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="editor">Section Description</label>
                                        <textarea class="form-control" id="editor" name="section_description" rows="5"
                                            placeholder="Enter Section Description">{{ old('section_description', $settings->section_description ?? '') }}</textarea>
                                    </div>

                                    {{-- ============ Motto ============ --}}
                                    <div class="col-12"><hr><h5 class="mb-0">Motto</h5></div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="motto">Motto</label>
                                        <input class="form-control" id="motto" type="text" name="motto"
                                            value="{{ old('motto', $settings->motto ?? '') }}" placeholder="Enter Motto">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="motto_image">Motto Image</label>
                                        <input class="form-control" id="motto_image" type="file" name="motto_image" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 2MB.</small>
                                        <div class="mt-2">
                                            <img id="motto_image-preview" src="{{ $mottoImg }}" data-existing="{{ $mottoImg }}" alt=""
                                                style="max-height:140px; max-width:220px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $mottoImg ? '' : 'display:none;' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="motto_description">Motto Description</label>
                                        <textarea class="form-control" id="motto_description" name="motto_description" rows="4"
                                            placeholder="Enter Motto Description">{{ old('motto_description', $settings->motto_description ?? '') }}</textarea>
                                    </div>

                                    {{-- ============ Board ============ --}}
                                    <div class="col-12"><hr><h5 class="mb-0">Board</h5></div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="board_heading">Board Heading</label>
                                        <input class="form-control" id="board_heading" type="text" name="board_heading"
                                            value="{{ old('board_heading', $settings->board_heading ?? '') }}" placeholder="Enter Board Heading">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="board_image">Board Image</label>
                                        <input class="form-control" id="board_image" type="file" name="board_image" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 2MB.</small>
                                        <div class="mt-2">
                                            <img id="board_image-preview" src="{{ $boardImg }}" data-existing="{{ $boardImg }}" alt=""
                                                style="max-height:140px; max-width:220px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $boardImg ? '' : 'display:none;' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="board_small_desc">Board Short Description</label>
                                        <textarea class="form-control" id="board_small_desc" name="board_small_desc" rows="3"
                                            placeholder="Enter a short description">{{ old('board_small_desc', $settings->board_small_desc ?? '') }}</textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="board_name">Name</label>
                                        <input class="form-control" id="board_name" type="text" name="board_name"
                                            value="{{ old('board_name', $settings->board_name ?? '') }}" placeholder="Enter Name">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="board_designation">Designation</label>
                                        <input class="form-control" id="board_designation" type="text" name="board_designation"
                                            value="{{ old('board_designation', $settings->board_designation ?? '') }}" placeholder="Enter Designation">
                                    </div>

                                    {{-- Board members repeater --}}
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Board Members</label>
                                            <button type="button" class="btn btn-sm btn-success" id="add-board-member">+ Add More</button>
                                        </div>
                                        @php $boardMembers = old('board_members', $settings->board_members ?? []); @endphp
                                        <table class="table table-bordered align-middle mb-2" id="board-members-table">
                                            <thead>
                                                <tr>
                                                    <th>Member Name</th>
                                                    <th style="width:120px;" class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="board-members-body">
                                                @forelse($boardMembers as $bmName)
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="board_members[]" value="{{ $bmName }}" placeholder="Member name"></td>
                                                        <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-board-member">Remove</button></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="board_members[]" value="" placeholder="Member name"></td>
                                                        <td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-board-member">Remove</button></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12"><hr><h5 class="mb-0">Team Member</h5></div>
                                @endif

                                <!-- Member Name -->
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Member Name <span class="txt-danger">*</span></label>
                                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name', $member->name) }}" placeholder="e.g. Dr. John Doe">
                                </div>

                                <!-- Image -->
                                <div class="col-md-6">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control">
                                    <small class="text-muted">Leave blank to keep current. jpg, jpeg, png, webp — max 2MB</small>
                                    <div class="mt-2">
                                        <img id="image-preview"
                                             src="{{ $hasImage ? asset('our-team/'.$member->image) : '' }}"
                                             alt=""
                                             data-existing="{{ $hasImage ? asset('our-team/'.$member->image) : '' }}"
                                             style="max-height:160px; max-width:240px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; {{ $hasImage ? '' : 'display:none;' }}">
                                    </div>
                                </div>

                                <!-- Designation -->
                                <div class="col-md-6">
                                    <label class="form-label" for="designation">Designation <span class="txt-danger">*</span></label>
                                    <textarea class="form-control ckeditor-init" id="designation" name="designation" rows="4" placeholder="e.g. Chief Veterinarian">{!! old('designation', $member->designation) !!}</textarea>
                                </div>

                                <!-- Education (optional) -->
                                <div class="col-md-6">
                                    <label class="form-label" for="education">Education</label>
                                    <input class="form-control" id="education" type="text" name="education" value="{{ old('education', $member->education) }}" placeholder="e.g. BVSc & AH, MVSc">
                                </div>

                                <!-- Social Media Link -->
                                <div class="col-md-12">
                                    <label class="form-label" for="social_media_link">Social Media Link</label>
                                    <input class="form-control" id="social_media_link" type="url" name="social_media_link" value="{{ old('social_media_link', $member->social_media_link) }}" placeholder="https://www.linkedin.com/in/yourprofile">
                                </div>

                                <!-- Bio (used on speciality pages) -->
                                <div class="col-md-12">
                                    <label class="form-label" for="bio">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="6"
                                        placeholder="Full doctor bio — used on speciality pages when this doctor is attached.">{{ old('bio', $member->bio) }}</textarea>
                                    <small class="text-muted">Optional. Shown on Speciality Detail pages when this doctor is attached. Can be overridden per speciality.</small>
                                </div>

                                <!-- Show on Team Page -->
                                <div class="col-md-12 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_on_team_page"
                                            name="show_on_team_page" value="1"
                                            {{ old('show_on_team_page', $member->show_on_team_page) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="show_on_team_page">
                                            Show on public Team page
                                        </label>
                                    </div>
                                    <small class="text-muted ms-3">Uncheck for guest specialists you only want to feature on speciality pages.</small>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 text-end">
                                    <a href="{{ route('manage-our-team.index') }}" class="btn btn-danger px-4">Cancel</a>
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
            // ---- Image previews (revert to existing image when cleared) ----
            function wirePreview(inputId, previewId) {
                var input   = document.getElementById(inputId);
                var preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file) {
                        var existing = preview.getAttribute('data-existing');
                        if (existing) {
                            preview.src = existing;
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
            }

            wirePreview('image',        'image-preview');
            wirePreview('banner_image', 'banner_image-preview');
            wirePreview('motto_image',  'motto_image-preview');
            wirePreview('board_image',  'board_image-preview');

            // ---- Board members repeater ----
            var body   = document.getElementById('board-members-body');
            var addBtn = document.getElementById('add-board-member');

            function boardRow() {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><input type="text" class="form-control" name="board_members[]" placeholder="Member name"></td>' +
                    '<td class="text-end"><button type="button" class="btn btn-sm btn-danger remove-board-member">Remove</button></td>';
                return tr;
            }

            if (addBtn && body) {
                addBtn.addEventListener('click', function () {
                    body.appendChild(boardRow());
                });

                body.addEventListener('click', function (e) {
                    if (!e.target.classList.contains('remove-board-member')) return;
                    var rows = body.querySelectorAll('tr');
                    if (rows.length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        var input = e.target.closest('tr').querySelector('input');
                        if (input) input.value = '';
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ---------- CKEditor: attach to every .ckeditor-init textarea (Designation) ---------- */
            var editorInstances = new Map();
            function initCKEditor(textarea) {
                if (!textarea || editorInstances.has(textarea) || typeof ClassicEditor === 'undefined') return;
                textarea.removeAttribute('required');
                ClassicEditor.create(textarea, {
                    toolbar: [
                        'bold', 'italic', 'underline', 'link',
                        'bulletedList', 'numberedList', '|',
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
        });
    </script>

</body>
</html>
