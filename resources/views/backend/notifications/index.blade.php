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
                    <div class="col-6"><h4>Notifications</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            {{-- Summary tiles --}}
            <div class="row g-3 mb-1">
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--primary">
                        <div><div class="dash-stat__num">{{ $summary['total'] }}</div><div class="dash-stat__label">Total</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-bell"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--warning">
                        <div><div class="dash-stat__num">{{ $summary['unread'] }}</div><div class="dash-stat__label">Unread</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-exclamation-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            <form id="ntFilterForm" class="mb-4">
                                @csrf
                                <div class="appt-filter-panel">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Source</label>
                                            <select name="source" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="website" {{ request('source') === 'website' ? 'selected' : '' }}>Website</option>
                                                <option value="whatsapp" {{ request('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Type</label>
                                            <select name="type" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="contact_enquiry" {{ request('type') === 'contact_enquiry' ? 'selected' : '' }}>Contact Enquiry</option>
                                                <option value="appointment_enquiry" {{ request('type') === 'appointment_enquiry' ? 'selected' : '' }}>Appointment Booking</option>
                                                <option value="job_application" {{ request('type') === 'job_application' ? 'selected' : '' }}>Job Application</option>
                                                <option value="whatsapp_booking" {{ request('type') === 'whatsapp_booking' ? 'selected' : '' }}>WhatsApp Booking</option>
                                                <option value="whatsapp_query" {{ request('type') === 'whatsapp_query' ? 'selected' : '' }}>WhatsApp Query</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Status</label>
                                            <select name="status" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-8 col-6">
                                            <label class="form-label small fw-semibold mb-1">Search</label>
                                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm js-auto-filter" placeholder="Title or details…">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" id="ntFilterReset" class="btn btn-outline-secondary btn-sm px-3">Reset</button>
                                        <button type="button" id="ntMarkAll" class="btn btn-outline-primary btn-sm px-3">Mark all read</button>
                                    </div>
                                </div>
                            </form>

                            <div id="ntResultsWrap" class="position-relative">
                                <div id="ntLoader" class="appt-loader d-none">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
                                </div>
                                <div id="ntResults">
                                    @include('backend.notifications._table')
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
            var $form = $('#ntFilterForm'), $results = $('#ntResults');
            var filterUrl = "{{ route('admin.notifications.filter') }}";
            var readAllUrl = "{{ route('admin.notifications.read-all') }}";
            var TOKEN = $('meta[name=csrf-token]').attr('content') || '';

            function loadResults(page) {
                var data = $form.serializeArray();
                if (page) data.push({ name: 'page', value: page });
                $('#ntLoader').removeClass('d-none'); $results.css('opacity', 0.35);
                $.ajax({ url: filterUrl, method: 'POST', data: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .done(function (html) { $results.html(html); })
                    .always(function () { $('#ntLoader').addClass('d-none'); $results.css('opacity', 1); });
            }

            $form.on('change', '.js-auto-filter', function () { loadResults(); });
            var t; $form.on('input', 'input[name=search]', function () { clearTimeout(t); t = setTimeout(loadResults, 400); });
            $form.on('submit', function (e) { e.preventDefault(); loadResults(); });
            $('#ntFilterReset').on('click', function () { $form.find('select').val(''); $form.find('input[name=search]').val(''); loadResults(); });
            $('#ntMarkAll').on('click', function () {
                $.ajax({ url: readAllUrl, method: 'POST', headers: { 'X-CSRF-TOKEN': TOKEN } }).done(function () { loadResults(); });
            });
            $results.on('click', '.pagination a', function (e) {
                e.preventDefault(); var m = ($(this).attr('href') || '').match(/[?&]page=(\d+)/); loadResults(m ? m[1] : 1);
            });
        })(jQuery);
    </script>
</body>
</html>
