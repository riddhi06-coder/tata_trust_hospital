<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $dbDriven = in_array($content->content_key, ['book.speciality', 'book.doctor'], true);
        $isMenu   = \Illuminate\Support\Str::startsWith($content->content_key, 'menu.');
    @endphp

    <div class="page-body cb-wrap">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Edit Chatbot Content</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-chatbot.index') }}">Chatbot Management</a></li>
                            <li class="breadcrumb-item active">{{ $content->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('manage-chatbot.update', $content->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header py-3">
                                <h5 class="mb-0">{{ $content->title }}</h5>
                                <small class="text-muted">{{ $content->section }}@unless($content->is_protected) · custom @endunless</small>
                            </div>
                            <div class="card-body">
                                @unless($content->is_protected)
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-9">
                                            <label class="form-label fw-semibold">{{ $content->flow === 'faq' ? 'FAQ title' : ($content->flow === 'menu' ? 'Menu button label' : 'Question name') }}</label>
                                            <input type="text" name="title" value="{{ old('title', $content->title) }}" class="form-control" maxlength="120">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Priority</label>
                                            <input type="number" name="sort_order" value="{{ old('sort_order', $content->sort_order) }}" class="form-control" min="0" max="9999">
                                            <small class="text-muted">Lower = first.</small>
                                        </div>
                                    </div>
                                @endunless

                                {{-- Message / prompt --}}
                                <label class="form-label fw-semibold">
                                    {{ $content->flow === 'faq' ? 'Answer' : ($content->flow === 'menu' ? 'Reply message' : 'Message / Question') }}
                                </label>
                                <div class="wa-editor mb-1">
                                    <div class="wa-toolbar">
                                        <button type="button" data-wa="bold" title="Bold"><strong>B</strong></button>
                                        <button type="button" data-wa="italic" title="Italic"><em>I</em></button>
                                        <button type="button" data-wa="strike" title="Strikethrough"><s>S</s></button>
                                        <button type="button" data-wa="bullet" title="Bullet list">•</button>
                                    </div>
                                    <textarea name="body" class="wa-input" placeholder="Leave blank to use the built-in default">{{ old('body', $content->body) }}</textarea>
                                    <div class="wa-preview-label">Live preview</div>
                                    <div class="wa-preview"></div>
                                </div>
                                <small class="text-muted d-block mb-4">Formatting: <code>*bold*</code>, <code>_italic_</code>, <code>~strike~</code>. Line breaks are kept.</small>

                                {{-- Options --}}
                                @if($dbDriven)
                                    <div class="alert alert-info d-flex align-items-start gap-2">
                                        <i class="fa fa-info-circle mt-1"></i>
                                        <div>
                                            The answer options here are pulled <strong>live from your team list</strong> — the bot shows
                                            {{ $content->content_key === 'book.doctor' ? 'doctors' : 'specialities' }} that are marked
                                            <em>“Selectable in WhatsApp”</em>. Manage them on the
                                            <a href="{{ route('manage-our-team.index') }}">Our Team</a> page. You can still edit the question wording above.
                                        </div>
                                    </div>
                                @elseif($content->has_options)
                                    <div class="opt-manager" data-menu="{{ $isMenu ? '1' : '0' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-semibold mb-0">Answer options <small class="text-muted">(drag ↕ to reorder)</small></label>
                                            <button type="button" class="btn btn-sm btn-outline-primary opt-add">+ Add option</button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead>
                                                    <tr>
                                                        <th style="width:36px"></th>
                                                        <th>Label (shown in chat)</th>
                                                        @if($isMenu)<th>Description</th>@endif
                                                        <th style="width:70px" class="text-center">Active</th>
                                                        <th style="width:44px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="opt-body">
                                                    @foreach(old('options', $content->options ?? []) as $opt)
                                                        <tr class="option-row">
                                                            <td class="text-center text-muted drag-handle">↕</td>
                                                            <td>
                                                                <input type="hidden" class="opt-id" value="{{ $opt['id'] ?? '' }}">
                                                                <input type="text" class="form-control form-control-sm opt-title" value="{{ $opt['title'] ?? '' }}">
                                                            </td>
                                                            @if($isMenu)
                                                                <td><input type="text" class="form-control form-control-sm opt-desc" value="{{ $opt['description'] ?? '' }}"></td>
                                                            @endif
                                                            <td class="text-center"><input type="checkbox" class="form-check-input opt-active" value="1" {{ ($opt['is_active'] ?? true) ? 'checked' : '' }}></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 rm-option">✕</button></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted d-block mb-3">Clear a label to remove it on save. Keep labels short — WhatsApp shows ~24 characters.</small>
                                    </div>
                                @endif

                                {{-- Active toggle --}}
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $content->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="is_active">Active (uncheck to fall back to the built-in default)</label>
                                </div>

                                <div class="d-flex gap-2 align-items-center">
                                    <button type="submit" class="btn btn-primary px-4">Save changes</button>
                                    <a href="{{ route('manage-chatbot.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    @unless($content->is_protected)
                                        <span class="ms-auto"></span>
                                    @endunless
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @unless($content->is_protected)
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <form action="{{ route('manage-chatbot.destroy', $content->id) }}" method="POST" onsubmit="return confirm('Delete this custom question? This cannot be undone.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete this custom question</button>
                        </form>
                    </div>
                </div>
            @endunless
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
    @include('backend.chatbot._editor_assets')
</body>
</html>
