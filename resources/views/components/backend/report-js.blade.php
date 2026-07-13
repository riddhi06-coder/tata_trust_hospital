{{-- Shared report behaviour: AJAX POST filtering (no reload), chart (re)rendering,
     CSV export link sync. Each report page must expose:
       #reportFilterForm  with data-filter-url + data-export-url and .js-auto-filter inputs
       #reportBodyWrap > #reportLoader + #reportBody (the body partial)
       #reportExport (CSV link), #reportReset (button)
     The body partial embeds <script type="application/json" id="reportChartData">…</script> --}}
<script>
    (function ($) {
        var $form = $('#reportFilterForm');
        if (!$form.length) return;

        var $body     = $('#reportBody');
        var filterUrl = $form.data('filter-url');
        var exportUrl = $form.data('export-url');

        function renderCharts() {
            if (window._reportCharts) {
                window._reportCharts.forEach(function (c) { try { c.destroy(); } catch (e) {} });
            }
            window._reportCharts = [];

            var el = document.getElementById('reportChartData');
            if (!el || typeof ApexCharts === 'undefined') return;

            var cfgs;
            try { cfgs = JSON.parse(el.textContent || '[]'); } catch (e) { return; }

            cfgs.forEach(function (cfg) {
                var node = document.getElementById(cfg.el);
                if (!node) return;

                var opt = {
                    chart: { type: cfg.type, height: cfg.height || 280, toolbar: { show: false } },
                    colors: cfg.colors,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: false },
                    stroke: { width: cfg.type === 'line' ? 3 : 1, curve: 'smooth' }
                };

                if (cfg.type === 'donut' || cfg.type === 'pie') {
                    opt.labels = cfg.labels;
                    opt.series = cfg.series;
                } else {
                    opt.series = cfg.series;
                    opt.xaxis = { categories: cfg.labels };
                    opt.plotOptions = { bar: { borderRadius: 4, columnWidth: '55%' } };
                }

                var c = new ApexCharts(node, opt);
                c.render();
                window._reportCharts.push(c);
            });
        }

        function syncExport() {
            if (!exportUrl) return;
            var params = $form.find(':input').not('[name=_token]').serialize();
            $('#reportExport').attr('href', exportUrl + (params ? ('?' + params) : ''));
        }

        function load(page) {
            var data = $form.serializeArray();
            if (page) data.push({ name: 'page', value: page });

            $('#reportLoader').removeClass('d-none');
            $body.css('opacity', 0.35);
            $.ajax({
                url: filterUrl,
                method: 'POST',
                data: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (html) { $body.html(html); renderCharts(); },
                complete: function () {
                    $('#reportLoader').addClass('d-none');
                    $body.css('opacity', 1);
                    syncExport();
                }
            });
        }

        // All dropdowns / dates apply on change; text inputs are debounced.
        $form.on('change', '.js-auto-filter', function () { load(); });
        var typingTimer;
        $form.on('input', 'input[type=text]', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () { load(); }, 400);
        });
        $form.on('submit', function (e) { e.preventDefault(); load(); });

        $('#reportReset').on('click', function () {
            $form.find('select').val('');
            $form.find('input[type=date], input[type=text]').val('');
            load();
        });

        $body.on('click', '.pagination a', function (e) {
            e.preventDefault();
            var href = $(this).attr('href') || '';
            var m = href.match(/[?&]page=(\d+)/);
            load(m ? m[1] : 1);
        });

        renderCharts();
        syncExport();
    })(jQuery);
</script>
