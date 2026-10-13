<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body cb-wrap">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Add New Item</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-chatbot.index') }}">Chatbot Management</a></li>
                            <li class="breadcrumb-item active">Add Item</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('manage-chatbot.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header py-3"><h5 class="mb-0">New question</h5></div>
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Add to</label>
                                        <select name="flow" id="flowSelect" class="form-control">
                                            <option value="booking" {{ old('flow','booking')==='booking'?'selected':'' }}>Appointment booking (question)</option>
                                            <option value="report" {{ old('flow')==='report'?'selected':'' }}>Medical report request (question)</option>
                                            <option value="faq" {{ old('flow')==='faq'?'selected':'' }}>FAQ (common questions)</option>
                                            <option value="menu" {{ old('flow')==='menu'?'selected':'' }}>Main menu option</option>
                                        </select>
                                        <small class="text-muted" id="flowHint">Booking: asked before day &amp; time. Report: asked at the end.</small>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold" id="lblTitle">Question name <span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" id="titleInput" placeholder="e.g. Is your pet on any medication?" required>
                                        <small class="text-muted" id="hintTitle">Short internal label (also used to tag the answer on the booking).</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Priority</label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order') }}" class="form-control" min="0" placeholder="Auto">
                                        <small class="text-muted">Lower = shown first. Blank = add to end.</small>
                                    </div>
                                </div>

                                <label class="form-label fw-semibold" id="lblBody">Question text <span class="text-danger">*</span></label>
                                <div class="wa-editor mb-3">
                                    <div class="wa-toolbar">
                                        <button type="button" data-wa="bold" title="Bold"><strong>B</strong></button>
                                        <button type="button" data-wa="italic" title="Italic"><em>I</em></button>
                                        <button type="button" data-wa="strike" title="Strikethrough"><s>S</s></button>
                                        <button type="button" data-wa="bullet" title="Bullet list">•</button>
                                    </div>
                                    <textarea name="body" class="wa-input" placeholder="What the bot asks the pet parent…">{{ old('body') }}</textarea>
                                    <div class="wa-preview-label">Live preview</div>
                                    <div class="wa-preview"></div>
                                </div>

                                <div id="questionOnly">
                                <label class="form-label fw-semibold">Answer type</label>
                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="input_type" id="type_text" value="text" {{ old('input_type','text')==='text'?'checked':'' }}>
                                        <label class="form-check-label" for="type_text">Free text (they type a reply)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="input_type" id="type_choice" value="choice" {{ old('input_type')==='choice'?'checked':'' }}>
                                        <label class="form-check-label" for="type_choice">Multiple choice (pick an option)</label>
                                    </div>
                                </div>

                                {{-- Options (shown only for multiple-choice) --}}
                                <div id="optionsSection" class="opt-manager mb-4" data-menu="0" style="display:none;">
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
                                                    <th style="width:70px" class="text-center">Active</th>
                                                    <th style="width:44px"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="opt-body">
                                                @forelse(old('options', []) as $opt)
                                                    <tr class="option-row">
                                                        <td class="text-center text-muted drag-handle">↕</td>
                                                        <td><input type="hidden" class="opt-id" value="{{ $opt['id'] ?? '' }}"><input type="text" class="form-control form-control-sm opt-title" value="{{ $opt['title'] ?? '' }}"></td>
                                                        <td class="text-center"><input type="checkbox" class="form-check-input opt-active" value="1" {{ ($opt['is_active'] ?? true) ? 'checked' : '' }}></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 rm-option">✕</button></td>
                                                    </tr>
                                                @empty
                                                    <tr class="option-row">
                                                        <td class="text-center text-muted drag-handle">↕</td>
                                                        <td><input type="hidden" class="opt-id" value=""><input type="text" class="form-control form-control-sm opt-title" placeholder="Option label"></td>
                                                        <td class="text-center"><input type="checkbox" class="form-check-input opt-active" value="1" checked></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 rm-option">✕</button></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted">Keep labels short — WhatsApp shows ~24 characters.</small>
                                </div>
                                </div>{{-- /#questionOnly --}}

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">Add question</button>
                                    <a href="{{ route('manage-chatbot.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header py-3"><h6 class="mb-0">How it works</h6></div>
                            <div class="card-body">
                                <p class="small text-muted mb-2">Custom questions are asked during the appointment booking, just before the day &amp; time step.</p>
                                <p class="small text-muted mb-0">The answer is saved with the appointment (shown in the booking details), so your team sees it alongside the rest of the request.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
    @include('backend.chatbot._editor_assets')

    <script>
        (function () {
            var section    = document.getElementById('optionsSection');
            var questionly = document.getElementById('questionOnly');
            var flowSel    = document.getElementById('flowSelect');
            var lblTitle   = document.getElementById('lblTitle');
            var hintTitle  = document.getElementById('hintTitle');
            var lblBody    = document.getElementById('lblBody');
            var titleInput = document.getElementById('titleInput');
            var submitBtn  = document.getElementById('submitBtn');

            var copy = {
                booking: { title:'Question name', hint:'Short label (also tags the answer on the booking).', body:'Question text', ph:'e.g. Is your pet on any medication?', btn:'Add question' },
                report:  { title:'Question name', hint:'Short label (also tags the answer on the report).',  body:'Question text', ph:'e.g. How urgent is this report?',       btn:'Add question' },
                faq:     { title:'FAQ title (shown in the list)', hint:'Short label shown in “Common questions”.', body:'Answer', ph:'e.g. Do you treat exotic pets?',    btn:'Add FAQ' },
                menu:    { title:'Menu button label', hint:'The button shown on the main menu.', body:'Reply message (sent when tapped)', ph:'e.g. Grooming prices', btn:'Add menu option' }
            };

            function toggleOptions() {
                var choice = document.getElementById('type_choice').checked;
                section.style.display = choice ? '' : 'none';
            }
            function applyFlow() {
                var f = flowSel.value;
                var c = copy[f] || copy.booking;
                var isQuestion = (f === 'booking' || f === 'report');
                questionly.style.display = isQuestion ? '' : 'none';
                lblTitle.innerHTML = c.title + ' <span class="text-danger">*</span>';
                hintTitle.textContent = c.hint;
                lblBody.innerHTML = c.body + ' <span class="text-danger">*</span>';
                titleInput.setAttribute('placeholder', c.ph);
                submitBtn.textContent = c.btn;
                if (isQuestion) toggleOptions();
            }

            document.getElementById('type_text').addEventListener('change', toggleOptions);
            document.getElementById('type_choice').addEventListener('change', toggleOptions);
            flowSel.addEventListener('change', applyFlow);
            applyFlow();
        })();
    </script>
</body>
</html>
