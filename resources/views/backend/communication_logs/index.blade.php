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
                    <div class="col-6"><h4>Communication Logs</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Communication Logs</li>
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
                        <div><div class="dash-stat__num">{{ $summary['total'] }}</div><div class="dash-stat__label">Total Messages</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-paper-plane"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--info">
                        <div><div class="dash-stat__num">{{ $summary['sms'] }}</div><div class="dash-stat__label">SMS</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-mobile"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--success">
                        <div><div class="dash-stat__num">{{ $summary['email'] }}</div><div class="dash-stat__label">Emails</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-envelope"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="dash-stat dash-stat--warning">
                        <div><div class="dash-stat__num">{{ $summary['failed'] }}</div><div class="dash-stat__label">Failed</div></div>
                        <div class="dash-stat__icon"><i class="fa fa-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Filters (AJAX POST; all fields auto-apply on change, no page reload) --}}
                            <form id="clFilterForm" class="mb-4">
                                @csrf
                                <div class="appt-filter-panel">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Channel</label>
                                            <select name="channel" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                                                <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Type</label>
                                            <select name="type" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                @foreach($types as $t)
                                                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $t)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Status</label>
                                            <select name="status" class="form-select form-select-sm js-auto-filter">
                                                <option value="">All</option>
                                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
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
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <label class="form-label small fw-semibold mb-1">Recipient / Subject</label>
                                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm js-auto-filter" placeholder="Email, mobile, name…">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" id="clFilterReset" class="btn btn-outline-secondary btn-sm px-3">Reset</button>
                                    </div>
                                </div>
                            </form>

                            {{-- AJAX-swappable results --}}
                            <div id="clResultsWrap" class="position-relative">
                                <div id="clLoader" class="appt-loader d-none">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
                                </div>
                                <div id="clResults">
                                    @include('backend.communication_logs._table')
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
            var $form    = $('#clFilterForm');
            var $results = $('#clResults');
            var filterUrl = "{{ route('admin.communication-logs.filter') }}";

            function loadResults(page) {
                var data = $form.serializeArray();
                if (page) { data.push({ name: 'page', value: page }); }

                $('#clLoader').removeClass('d-none');
                $results.css('opacity', 0.35);
                $.ajax({
                    url: filterUrl,
                    method: 'POST',
                    data: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (html) { $results.html(html); },
                    complete: function () {
                        $('#clLoader').addClass('d-none');
                        $results.css('opacity', 1);
                    }
                });
            }

            // Every filter field applies on change (selects, dates, and search).
            $form.on('change', '.js-auto-filter', function () { loadResults(); });
            // Also apply as the user types in the search box (debounced).
            var typingTimer;
            $form.on('input', 'input[name=search]', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () { loadResults(); }, 400);
            });
            // Never allow a native GET submit.
            $form.on('submit', function (e) { e.preventDefault(); loadResults(); });

            $('#clFilterReset').on('click', function () {
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
