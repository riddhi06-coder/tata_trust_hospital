<!DOCTYPE html>
<html lang="en">
    <head>
        @include('components.frontend.head')
    </head>
  <body>

    @include('components.frontend.header')


    <!-- main-area -->
    <main class="fix">


        <!-- Breadcrumb -->
        <section class="breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                <img src="{{ asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">Book An Appointment</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Book An Appointment</li>
                </ul>
            </div>
        </section>


        <section class="book-an-appointment-custom-one-sec">
            <div class="container px-3">
                    <div class="baapo-custom-title-sec">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title">Book Appointment</h2>
                        </div>
                    </div>

                <div class="form-card">

                    <form id="apptForm" novalidate>
                        @csrf

                        <!-- ── OWNER INFO ── -->
                        <div class="field-divider">
                            <span>Owner Information</span>
                        </div>
                        <div class="form-body">
                            <div class="row g-3">

                                <div class="col-12 col-sm-4">
                                    <label class="form-label">Your Name <span class="req">*</span></label>
                                    <input type="text" name="name" id="apptName" class="form-control"
                                           value="{{ optional($user)->name }}"
                                           placeholder="Full name" maxlength="100" />
                                    <small class="error-msg" id="err_name"></small>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label class="form-label">Mobile Number <span class="req">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">+91</span>
                                        <input type="tel" name="mobile" id="apptMobile" class="form-control"
                                               value="{{ $mobile }}"
                                               maxlength="10" pattern="[0-9]{10}" readonly />
                                    </div>
                                    <small class="text-muted">Verified via OTP</small>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label class="form-label">Email ID <span class="req">*</span></label>
                                    <input type="email" name="email" id="apptEmail" class="form-control"
                                           value="{{ optional($user)->email }}"
                                           placeholder="you@example.com" maxlength="150" />
                                    <small class="error-msg" id="err_email"></small>
                                </div>

                                <div class="col-12 col-sm-8">
                                    <label class="form-label">Address <span class="req">*</span></label>
                                    <input type="text" name="address" id="apptAddress" class="form-control"
                                           value="{{ optional($user)->address }}"
                                           placeholder="Enter Address" maxlength="255" />
                                    <small class="error-msg" id="err_address"></small>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label class="form-label">Pin Code <span class="req">*</span></label>
                                    <input type="tel" name="pincode" id="apptPincode" class="form-control"
                                           value="{{ optional($user)->pincode }}"
                                           placeholder="6-digit PIN" maxlength="6" inputmode="numeric"
                                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,6);" />
                                    <small class="error-msg" id="err_pincode"></small>
                                </div>

                            </div>
                        </div>

                        <!-- ── PET DETAILS ── -->
                        <div class="field-divider">
                            <span>Pet Details</span>
                        </div>
                        <div class="form-body">
                            <div class="row g-3">

                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Pet Name <span class="req">*</span></label>
                                    <input type="text" name="pet_name" id="apptPetName" class="form-control" placeholder="e.g. Bruno / Mochi" maxlength="100" />
                                    <small class="error-msg" id="err_pet_name"></small>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Age / DOB of Pet</label>
                                    <input type="text" name="pet_age" id="apptPetAge" class="form-control" placeholder="e.g. 2 yrs or 14 Jan 2022" maxlength="60" />
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label d-block mb-2">My Pet Is <span class="req">*</span></label>
                                    <div class="toggle-group">
                                        <input type="radio" name="pet_type" id="petDog" value="dog" />
                                        <label for="petDog"><img src="{{ asset('frontend/assets/img/icon/dog-icon.webp') }}" alt=""> Dog</label>
                                        <input type="radio" name="pet_type" id="petCat" value="cat" />
                                        <label for="petCat"><img src="{{ asset('frontend/assets/img/icon/cat-icon.webp') }}" alt=""> Cat</label>
                                    </div>
                                    <small class="error-msg" id="err_pet_type"></small>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label d-block mb-2">Gender <span class="req">*</span></label>
                                    <div class="toggle-group">
                                        <input type="radio" name="pet_gender" id="genderMale" value="male" />
                                        <label for="genderMale"><img src="{{ asset('frontend/assets/img/icon/male-icon.webp') }}" alt=""> Male</label>
                                        <input type="radio" name="pet_gender" id="genderFemale" value="female" />
                                        <label for="genderFemale"><img src="{{ asset('frontend/assets/img/icon/female-icon.webp') }}" alt=""> Female</label>
                                    </div>
                                    <small class="error-msg" id="err_pet_gender"></small>
                                </div>

                            </div>
                        </div>

                        <!-- ── CONSULTATION ── -->
                        <div class="field-divider">
                            <span>Consultation Details</span>
                        </div>
                        <div class="form-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label d-block mb-2">Type of Consultation <span class="req">*</span></label>
                                    <div class="toggle-group">
                                        <input type="radio" name="consult_type" id="typeFirst" value="first" />
                                        <label for="typeFirst"><img src="{{ asset('frontend/assets/img/icon/first-time-consultation-icon.webp') }}" alt=""> First-time Consultation</label>
                                        <input type="radio" name="consult_type" id="typeFollowup" value="followup" />
                                        <label for="typeFollowup"><img src="{{ asset('frontend/assets/img/icon/follow-up--visit-icon-.webp') }}" alt=""> Follow-up Visit</label>
                                    </div>
                                    <small class="error-msg" id="err_consult_type"></small>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Reason for Consultation <span class="req">*</span></label>
                                    <textarea name="reason" id="apptReason" class="form-control" rows="1"
                                              placeholder="Describe symptoms, concerns, or reason for the visit…" maxlength="2000"></textarea>
                                    <small class="error-msg" id="err_reason"></small>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Date of Appointment <span class="req">*</span></label>
                                    <input type="date" name="appointment_date" id="apptDate" class="form-control" min="{{ now()->format('Y-m-d') }}" />
                                    <small class="error-msg" id="err_appointment_date"></small>
                                </div>

                                <div class="col-12 mt-4 text-center">
                                    <button type="submit" id="apptSubmitBtn" class="btn">
                                        Submit <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable">
                                    </button>
                                </div>

                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </section>

    </main>
    <!-- main-area-end -->

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

    <script>
    (function () {
        var form = document.getElementById('apptForm');
        if (!form) return;

        function show(id, msg) {
            var e = document.getElementById('err_' + id);
            var i = form.querySelector('[name="' + id + '"]');
            if (e) e.textContent = msg;
            if (i) i.classList.add('is-invalid');
        }
        function clearErr(id) {
            var e = document.getElementById('err_' + id);
            var i = form.querySelector('[name="' + id + '"]');
            if (e) e.textContent = '';
            if (i) i.classList.remove('is-invalid');
        }
        var fieldsWithErrors = ['name','email','address','pincode','pet_name','pet_type','pet_gender','consult_type','reason','appointment_date'];

        // Live-clear on input/change.
        fieldsWithErrors.forEach(function (n) {
            var inputs = form.querySelectorAll('[name="' + n + '"]');
            inputs.forEach(function (i) {
                i.addEventListener('input',  function () { clearErr(n); });
                i.addEventListener('change', function () { clearErr(n); });
            });
        });

        // Belt-and-suspenders: also enforce min=today on the date picker in JS
        // (some older browsers ignore the HTML min attribute on <input type="date">).
        var $apptDate = document.getElementById('apptDate');
        if ($apptDate) {
            var t = new Date();
            var y = t.getFullYear();
            var m = String(t.getMonth() + 1).padStart(2, '0');
            var d = String(t.getDate()).padStart(2, '0');
            $apptDate.setAttribute('min', y + '-' + m + '-' + d);
        }

        function validate() {
            fieldsWithErrors.forEach(clearErr);
            var v = function (n) {
                var el = form.querySelector('[name="' + n + '"]');
                return el ? (el.value || '').trim() : '';
            };
            var errs = 0;

            var name = v('name');
            if (name === '') { show('name', 'Please enter your name.'); errs++; }
            else if (!/^[A-Za-z\s.'\-]+$/.test(name)) { show('name', 'Name cannot contain numbers or special characters.'); errs++; }

            var email = v('email');
            if (email === '') { show('email', 'Please enter your email.'); errs++; }
            else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) { show('email', 'Please enter a valid email address.'); errs++; }

            if (v('address') === '') { show('address', 'Please enter your address.'); errs++; }

            // Indian PIN: exactly 6 digits, first digit 1-9.
            var pin = v('pincode');
            if (pin === '') { show('pincode', 'Please enter your PIN code.'); errs++; }
            else if (!/^[1-9][0-9]{5}$/.test(pin)) { show('pincode', 'Enter a valid 6-digit PIN code.'); errs++; }

            if (v('pet_name') === '') { show('pet_name', "Please enter your pet's name."); errs++; }

            if (! form.querySelector('[name="pet_type"]:checked'))    { show('pet_type',    'Please select pet type.'); errs++; }
            if (! form.querySelector('[name="pet_gender"]:checked'))  { show('pet_gender',  'Please select pet gender.'); errs++; }
            if (! form.querySelector('[name="consult_type"]:checked')){ show('consult_type','Please select a consultation type.'); errs++; }

            if (v('reason') === '') { show('reason', 'Please describe the reason for the visit.'); errs++; }

            var apptDate = v('appointment_date');
            if (apptDate === '') {
                show('appointment_date', 'Please pick an appointment date.'); errs++;
            } else {
                var chosen = new Date(apptDate + 'T00:00:00');
                var todayMid = new Date(); todayMid.setHours(0,0,0,0);
                if (chosen < todayMid) { show('appointment_date', 'Appointment date cannot be in the past.'); errs++; }
            }

            return errs === 0;
        }

        var SUBMIT_URL = @json(route('frontend.appointment_store'));
        var CSRF = document.querySelector('input[name="_token"]')
                        ? document.querySelector('input[name="_token"]').value
                        : (document.querySelector('meta[name="csrf-token"]')
                                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                : '');

        var $submit = document.getElementById('apptSubmitBtn');
        var submitHtml = $submit.innerHTML;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validate()) {
                var first = form.querySelector('.is-invalid');
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            $submit.disabled = true;
            $submit.innerHTML = 'Submitting…';

            var fd = new FormData(form);
            fd.append('_token', CSRF);

            fetch(SUBMIT_URL, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: fd
            })
            .then(function (r) {
                return r.json().then(function (json) { return { status: r.status, data: json }; });
            })
            .then(function (res) {
                if (res.status >= 200 && res.status < 300 && res.data && res.data.success) {
                    window.location.href = res.data.redirect;
                    return;
                }
                if (res.status === 401 && res.data && res.data.redirect) {
                    window.location.href = res.data.redirect;
                    return;
                }
                // Validation errors: map to inline field errors.
                if (res.status === 422 && res.data && res.data.errors) {
                    Object.keys(res.data.errors).forEach(function (k) {
                        show(k, res.data.errors[k][0]);
                    });
                    var first = form.querySelector('.is-invalid');
                    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    alert((res.data && res.data.message) || 'Something went wrong. Please try again.');
                }
                $submit.disabled = false;
                $submit.innerHTML = submitHtml;
            })
            .catch(function () {
                alert('Network error. Please try again.');
                $submit.disabled = false;
                $submit.innerHTML = submitHtml;
            });
        });
    })();
    </script>

  </body>
</html>
