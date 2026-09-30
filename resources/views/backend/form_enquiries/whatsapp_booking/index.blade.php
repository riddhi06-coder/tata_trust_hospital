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
                    <div class="col-6"><h4>WhatsApp Bookings</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">Form Enquiries</li>
                            <li class="breadcrumb-item active">WhatsApp Bookings</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row mt-1">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Filters (AJAX POST; all fields auto-apply on change, no page reload) --}}
                            <form id="wbFilterForm" class="mb-4">
                                @csrf
                                <div class="appt-filter-panel">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Client Type</label>
                                            <select name="client_type" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="new" {{ request('client_type') === 'new' ? 'selected' : '' }}>New</option>
                                                <option value="existing" {{ request('client_type') === 'existing' ? 'selected' : '' }}>Existing</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">From</label>
                                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm js-auto-filter">
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">To</label>
                                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm js-auto-filter">
                                        </div>
                                        <div class="col-lg-4 col-md-8 col-6">
                                            <label class="form-label small fw-semibold mb-1">Search</label>
                                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm js-auto-filter" placeholder="Parent, pet, mobile…">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" id="wbFilterReset" class="btn btn-outline-secondary btn-sm px-3">Reset</button>
                                    </div>
                                </div>
                            </form>

                            {{-- AJAX-swappable results --}}
                            <div id="wbResultsWrap" class="position-relative">
                                <div id="wbLoader" class="appt-loader d-none">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
                                </div>
                                <div id="wbResults">
                                    @include('backend.form_enquiries.whatsapp_booking._table')
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
        (function ($) {
            var $form    = $('#wbFilterForm');
            var $results = $('#wbResults');
            var filterUrl = "{{ route('manage-whatsapp-bookings.filter') }}";

            function loadResults(page) {
                var data = $form.serializeArray();
                if (page) { data.push({ name: 'page', value: page }); }

                $('#wbLoader').removeClass('d-none');
                $results.css('opacity', 0.35);
                $.ajax({
                    url: filterUrl,
                    method: 'POST',
                    data: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (html) { $results.html(html); },
                    complete: function () {
                        $('#wbLoader').addClass('d-none');
                        $results.css('opacity', 1);
                    }
                });
            }

            $form.on('change', '.js-auto-filter', function () { loadResults(); });
            var typingTimer;
            $form.on('input', 'input[name=search]', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () { loadResults(); }, 400);
            });
            $form.on('submit', function (e) { e.preventDefault(); loadResults(); });

            $('#wbFilterReset').on('click', function () {
                $form.find('select').val('');
                $form.find('input[type=date], input[name=search]').val('');
                loadResults();
            });

            $results.on('click', '.pagination a', function (e) {
                e.preventDefault();
                var href = $(this).attr('href') || '';
                var m = href.match(/[?&]page=(\d+)/);
                loadResults(m ? m[1] : 1);
            });
        })(jQuery);
    </script>
</body>
</html>
