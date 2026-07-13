<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
    @include('components.backend.appointment-styles')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Appointments</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">Appointments</li>
                            <li class="breadcrumb-item active">Appointments</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <h5 class="mb-0">All Appointments</h5>
                                <a href="{{ route('manage-appointments.export') }}" id="exportBtn" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i> Download CSV
                                </a>
                            </div>

                            {{-- Filters (AJAX; no page reload) --}}
                            <form id="filterForm" class="mb-4">
                                @csrf
                                <div class="appt-filter-panel">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Status</label>
                                            <select name="status" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}" {{ (string) $filters['status'] === (string) $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Pet Type</label>
                                            <select name="pet_type" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="dog" {{ $filters['pet_type'] === 'dog' ? 'selected' : '' }}>Dog</option>
                                                <option value="cat" {{ $filters['pet_type'] === 'cat' ? 'selected' : '' }}>Cat</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Consultation</label>
                                            <select name="consult_type" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="first" {{ $filters['consult_type'] === 'first' ? 'selected' : '' }}>First-time</option>
                                                <option value="followup" {{ $filters['consult_type'] === 'followup' ? 'selected' : '' }}>Follow-up</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Appt. Date From</label>
                                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control form-control-sm js-auto-filter">
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Appt. Date To</label>
                                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control form-control-sm js-auto-filter">
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Search</label>
                                            <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control form-control-sm" placeholder="Owner / mobile / pet…">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm px-3">Search</button>
                                        <button type="button" id="filterReset" class="btn btn-outline-secondary btn-sm px-3">Reset</button>
                                    </div>
                                </div>
                            </form>

                            {{-- AJAX-swappable results --}}
                            <div id="apptResultsWrap" class="position-relative">
                                <div id="apptLoader" class="appt-loader d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading…</span>
                                    </div>
                                </div>
                                <div id="apptResults">
                                    @include('backend.appointments.module._table')
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status update modal --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="statusForm" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Appointment Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Appointment <strong id="modalRef"></strong> — <span id="modalOwner"></span></p>

                        <div class="mb-3">
                            <label class="form-label">New Status <span class="text-danger">*</span></label>
                            <select name="appointment_status_id" id="modalStatusSelect" class="form-select" required>
                                @foreach($statuses as $status)
                                    @if($status->is_active)
                                        <option value="{{ $status->id }}" data-requires-date="{{ $status->requires_appointment_date ? 1 : 0 }}">{{ $status->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="rescheduleDateWrap">
                            <label class="form-label">New Appointment Date <span class="text-danger">*</span></label>
                            <input type="date" name="appointment_date" id="rescheduleDate" class="form-control" min="{{ now()->toDateString() }}">
                            <small class="text-muted">Must be a future date and different from the current appointment date.</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Note / Reason <span class="text-muted">(optional)</span></label>
                            <textarea name="note" class="form-control" rows="3" maxlength="2000"
                                      placeholder="Why is this status being changed? (visible in the history trail)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
        (function ($) {
            var $form    = $('#filterForm');
            var $results = $('#apptResults');
            var filterUrl = "{{ route('manage-appointments.filter') }}";
            var exportUrl = "{{ route('manage-appointments.export') }}";

            // Keep the CSV link in sync with the active filters.
            function syncExport() {
                var params = $form.find(':input').not('[name=_token]').serialize();
                $('#exportBtn').attr('href', exportUrl + (params ? ('?' + params) : ''));
            }

            // Load results via AJAX (POST) without reloading the page.
            function loadResults(page) {
                var data = $form.serializeArray();
                if (page) { data.push({ name: 'page', value: page }); }

                $('#apptLoader').removeClass('d-none');
                $results.css('opacity', 0.35);
                $.ajax({
                    url: filterUrl,
                    method: 'POST',
                    data: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (html) { $results.html(html); },
                    complete: function () {
                        $('#apptLoader').addClass('d-none');
                        $results.css('opacity', 1);
                        syncExport();
                    }
                });
            }

            // Dropdowns + date pickers apply immediately.
            $form.on('change', '.js-auto-filter', function () { loadResults(); });

            // Search button / Enter key.
            $form.on('submit', function (e) { e.preventDefault(); loadResults(); });

            // Reset clears every field and reloads results (no page reload).
            $('#filterReset').on('click', function () {
                $form.find('select').val('');
                $form.find('input[type=date], input[name=search]').val('');
                loadResults();
            });

            // Pagination links inside the results are handled via AJAX too.
            $results.on('click', '.pagination a', function (e) {
                e.preventDefault();
                var href = $(this).attr('href') || '';
                var m = href.match(/[?&]page=(\d+)/);
                loadResults(m ? m[1] : 1);
            });

            syncExport();

            /* ---- Status update modal (delegated so AJAX-added rows work) ---- */
            var base = "{{ url('manage-appointments') }}";

            // Show/require the new-date field only for reschedule-type statuses.
            function toggleRescheduleDate() {
                var opt = $('#modalStatusSelect option:selected');
                var needs = opt.data('requires-date') == 1;
                if (needs) {
                    $('#rescheduleDateWrap').removeClass('d-none');
                    $('#rescheduleDate').prop('required', true);
                } else {
                    $('#rescheduleDateWrap').addClass('d-none');
                    $('#rescheduleDate').prop('required', false).val('');
                }
            }

            $('#modalStatusSelect').on('change', toggleRescheduleDate);

            $(document).on('click', '.js-update-status', function () {
                $('#statusForm').attr('action', base + '/' + $(this).data('id') + '/status');
                $('#modalRef').text($(this).data('ref') || '');
                $('#modalOwner').text($(this).data('owner') || '');
                var cur = $(this).data('status');
                if (cur) { $('#modalStatusSelect').val(cur); }
                toggleRescheduleDate();
            });
        })(jQuery);
    </script>
</body>
</html>
