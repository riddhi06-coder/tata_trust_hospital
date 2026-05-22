<!-- Slim JS bundle for auth pages: jquery + bootstrap + feather + notify -->
<script src="{{ asset('admin/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
<script src="{{ asset('admin/assets/js/notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/notify/index.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show / hide password
        document.querySelectorAll('.show-hide .show').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                var wrapper = toggle.closest('.form-input');
                if (!wrapper) return;
                var input = wrapper.querySelector('input[type="password"], input[type="text"]');
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        // Anti double-submit: disable submit button + show "Submitting..." after first click
        document.querySelectorAll('form.theme-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
                    if (btn.dataset.submitGuard === '1') return;
                    btn.dataset.submitGuard = '1';
                    btn.dataset.originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
                });
            });
        });

        // If validation failed and the page was re-rendered, re-enable any guarded buttons
        // (defensive; usually the page is a fresh render so buttons start enabled)
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('button[data-submit-guard="1"], input[data-submit-guard="1"]').forEach(function (btn) {
                btn.disabled = false;
                btn.dataset.submitGuard = '0';
                if (btn.dataset.originalText) {
                    btn.innerHTML = btn.dataset.originalText;
                }
            });
        });
    });
</script>

@if (session('message'))
    <script>
        (function ($) {
            "use strict";
            $.notify(
                '<i class="fa fa-bell-o"></i><strong>{{ session('message') }}</strong>',
                {
                    type: "theme",
                    allow_dismiss: true,
                    delay: 5000,
                    showProgressbar: true,
                    timer: 300,
                    animate: { enter: "animated fadeInDown", exit: "animated fadeOutUp" },
                }
            );
        })(jQuery);
    </script>
@endif

@if ($errors->any())
    <script>
        (function ($) {
            "use strict";
            $.notify(
                '<i class="fa fa-bell-o"></i><strong>@foreach ($errors->all() as $error){{ $error }}<br>@endforeach</strong>',
                {
                    type: "theme",
                    allow_dismiss: true,
                    delay: 5000,
                    showProgressbar: true,
                    timer: 300,
                    animate: { enter: "animated fadeInDown", exit: "animated fadeOutUp" },
                }
            );
        })(jQuery);
    </script>
@endif
