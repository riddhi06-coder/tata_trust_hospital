
<!DOCTYPE html>
<html lang="en">
  <head>
        @include('components.frontend.head')
  </head>
  <body>

    @include('components.frontend.header')

    <!-- main-area -->
    <main class="fix">

        <section class="login-custom-one-sec">
            <div class="container-fluid px-0">
                <div class="login-custom-wrapper">
                    <div class="login-custom-building-img">
                        <img src="{{ asset('frontend/assets/img/banner/contact-new-banner.webp') }}" alt="Hospital Building">
                    </div>
                    <div class="login-cta-box">
                        <div class="login-cta-img-sec">
                            <img src="{{ asset('frontend/assets/img/logo/tata-trust-logo.webp') }}" alt="">
                        </div>
                        <h4>Book An Appointment</h4>

                        <form id="loginForm" novalidate>
                            @csrf

                            <div id="loginFlash" class="login-flash" style="display:none;" role="alert"></div>

                            <!-- Mobile step -->
                            <div id="mobile-step">
                                <div class="mb-3">
                                    <input type="tel" id="mobile" class="form-control"
                                           placeholder="Enter Mobile Number"
                                           maxlength="10"
                                           inputmode="numeric"
                                           pattern="[0-9]{10}"
                                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                                    <small id="mobile-error" class="text-danger d-none"></small>
                                </div>
                                <button type="button" id="sendOtpBtn" class="btn">
                                    <span class="btn-text">Send OTP</span>
                                    <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable">
                                </button>
                            </div>

                            <!-- OTP step (hidden initially) -->
                            <div id="otp-step" style="display:none;">
                                <p class="mb-2">
                                    Enter the OTP sent to <strong id="otp-mobile-display"></strong>
                                    <a href="#" id="edit-mobile" style="font-size:12px; margin-left:6px;">Change</a>
                                </p>
                                <div class="mb-3">
                                    <input type="tel" id="otp" class="form-control"
                                           placeholder="Enter 6-digit OTP"
                                           maxlength="6"
                                           inputmode="numeric"
                                           pattern="[0-9]{6}"
                                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,6);">
                                    <small id="otp-error" class="text-danger d-none"></small>
                                </div>
                                <button type="button" id="verifyOtpBtn" class="btn">
                                    <span class="btn-text">Verify OTP</span>
                                </button>

                                <!-- Resend timer / link -->
                                <p class="mt-3" style="font-size:13px;">
                                    <span id="resend-timer"></span>
                                    <a href="#" id="resend-otp" style="display:none;">Resend OTP</a>
                                </p>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- main-area-end -->

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

    <script>
    (function () {
        var SEND_URL   = @json(route('frontend.send_otp'));
        var VERIFY_URL = @json(route('frontend.verify_otp'));
        var CSRF       = document.querySelector('input[name="_token"]')
                             ? document.querySelector('input[name="_token"]').value
                             : (document.querySelector('meta[name="csrf-token"]')
                                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    : '');

        var mobileStep    = document.getElementById('mobile-step');
        var otpStep       = document.getElementById('otp-step');
        var $mobile       = document.getElementById('mobile');
        var $mobileErr    = document.getElementById('mobile-error');
        var $otp          = document.getElementById('otp');
        var $otpErr       = document.getElementById('otp-error');
        var $sendBtn      = document.getElementById('sendOtpBtn');
        var $verifyBtn    = document.getElementById('verifyOtpBtn');
        var $mobileDisp   = document.getElementById('otp-mobile-display');
        var $editMobile   = document.getElementById('edit-mobile');
        var $resendTimer  = document.getElementById('resend-timer');
        var $resendLink   = document.getElementById('resend-otp');
        var $flash        = document.getElementById('loginFlash');

        var sendBtnHtml   = $sendBtn.innerHTML;
        var verifyBtnHtml = $verifyBtn.innerHTML;
        var timerHandle   = null;
        var currentMobile = '';

        function showErr(el, msg) { if (el) { el.textContent = msg; el.classList.remove('d-none'); } }
        function clearErr(el)     { if (el) { el.textContent = '';  el.classList.add('d-none'); } }

        function showFlash(msg, type) {
            $flash.className = 'login-flash login-flash--' + (type || 'info');
            $flash.textContent = msg;
            $flash.style.display = 'block';
            $flash.classList.remove('is-hiding');
            setTimeout(function () { $flash.classList.add('is-hiding'); }, 3500);
            setTimeout(function () { $flash.style.display = 'none'; }, 4000);
        }

        function startResendTimer(seconds) {
            if (timerHandle) clearInterval(timerHandle);
            $resendLink.style.display = 'none';
            var s = seconds;
            function tick() {
                if (s <= 0) {
                    clearInterval(timerHandle);
                    $resendTimer.textContent = '';
                    $resendLink.style.display = 'inline';
                    return;
                }
                $resendTimer.textContent = 'Resend OTP in ' + s + 's';
                s--;
            }
            tick();
            timerHandle = setInterval(tick, 1000);
        }

        function apiPost(url, data) {
            var fd = new FormData();
            Object.keys(data).forEach(function (k) { fd.append(k, data[k]); });
            fd.append('_token', CSRF);
            return fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: fd
            }).then(function (r) {
                return r.json().then(function (json) { return { status: r.status, data: json }; });
            });
        }

        function sendOtp(isResend) {
            var mobile = ($mobile.value || '').replace(/\D/g, '');
            if (mobile.length !== 10) {
                showErr($mobileErr, 'Enter a valid 10-digit mobile number.');
                return;
            }
            clearErr($mobileErr);

            if (!isResend) { $sendBtn.disabled = true; $sendBtn.innerHTML = 'Sending…'; }

            apiPost(SEND_URL, { mobile: mobile })
                .then(function (res) {
                    if (res.status >= 200 && res.status < 300 && res.data && res.data.success) {
                        currentMobile = res.data.mobile || mobile;
                        $mobileDisp.textContent = '+91 ' + currentMobile;
                        mobileStep.style.display = 'none';
                        otpStep.style.display    = 'block';
                        $otp.value = '';
                        clearErr($otpErr);
                        startResendTimer(60);
                        showFlash(res.data.message || 'OTP sent.', res.data.delivered === false ? 'error' : 'success');
                        setTimeout(function () { $otp.focus(); }, 100);
                    } else if (res.status === 429) {
                        showErr($mobileErr, (res.data && res.data.message) || 'Too many requests. Please wait.');
                    } else {
                        showErr($mobileErr, (res.data && res.data.message) || 'Could not send OTP. Try again.');
                    }
                })
                .catch(function () { showErr($mobileErr, 'Network error. Try again.'); })
                .finally(function () {
                    $sendBtn.disabled = false;
                    $sendBtn.innerHTML = sendBtnHtml;
                });
        }

        function verifyOtp() {
            var code = ($otp.value || '').replace(/\D/g, '');
            if (code.length !== 6) {
                showErr($otpErr, 'Enter the 6-digit OTP.');
                return;
            }
            clearErr($otpErr);
            $verifyBtn.disabled = true;
            $verifyBtn.innerHTML = 'Verifying…';

            apiPost(VERIFY_URL, { mobile: currentMobile, otp: code })
                .then(function (res) {
                    if (res.status >= 200 && res.status < 300 && res.data && res.data.success) {
                        showFlash('Verified! Redirecting…', 'success');
                        window.location.href = res.data.redirect;
                    } else {
                        showErr($otpErr, (res.data && res.data.message) || 'Verification failed.');
                    }
                })
                .catch(function () { showErr($otpErr, 'Network error. Try again.'); })
                .finally(function () {
                    $verifyBtn.disabled = false;
                    $verifyBtn.innerHTML = verifyBtnHtml;
                });
        }

        $sendBtn.addEventListener('click', function () { sendOtp(false); });
        $verifyBtn.addEventListener('click', verifyOtp);
        $mobile.addEventListener('input', function () { clearErr($mobileErr); });
        $otp.addEventListener('input',    function () { clearErr($otpErr); });

        $resendLink.addEventListener('click', function (e) {
            e.preventDefault();
            sendOtp(true);
        });

        $editMobile.addEventListener('click', function (e) {
            e.preventDefault();
            mobileStep.style.display = 'block';
            otpStep.style.display    = 'none';
            if (timerHandle) clearInterval(timerHandle);
            $mobile.focus();
        });

        $mobile.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); sendOtp(false); } });
        $otp.addEventListener('keydown',    function (e) { if (e.key === 'Enter') { e.preventDefault(); verifyOtp(); } });
    })();
    </script>

  </body>
</html>
