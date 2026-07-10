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
        <section class="breadcrumb join-us-breadcrumb-bg">
            <div class="breadcrumb-img-custom-sec">
                @if($join_page && $join_page->banner_image)
                    <img src="{{ asset('home/join-us/banner/'.$join_page->banner_image) }}" alt="">
                @else
                    <img src="{{ asset('assets/img/banner/about-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">
                    @if($join_page && $join_page->banner_heading)
                        {!! nl2br($join_page->banner_heading) !!}
                    @else
                        Be A Part Of <br>Our Team!
                    @endif
                </h1>
                <ul class="breadcrumb-nav">
                    <li><a href="./">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Join Us</li>
                </ul>
            </div>
        </section>

        <section class="join-us-one-sec">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <h2 class="title" data-aos="fade-up" data-aos-delay="150">{{ $join_page->section_heading ?? 'Join Us' }}</h2>
                        </div>
                        <div class="para-mb text-center" data-aos="fade-up" data-aos-delay="150">
                            {!! $join_page->section_description ?? '' !!}
                        </div>
                    </div>

                    @if($join_page && $join_page->infos->count())
                        @foreach($join_page->infos as $i => $info)
                            <div class="col-lg-6">
                                <div class="join-us-why-choose-what-we-look-sec" data-aos="fade-up" data-aos-delay="{{ 150 + ($i * 100) }}">
                                    <div class="feature-one__single-inner">
                                        @if($info->image)
                                            <div class="juwcwwl-img-sec">
                                                <img src="{{ asset('home/join-us/info/'.$info->image) }}" alt="{{ $info->title }}">
                                            </div>
                                        @endif
                                        <h3 class="feature-one__title">{{ $info->title }}</h3>
                                        <div class="feature-one__text">{!! $info->description !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </section>



        <section class="current-job-openings-section">
            <div class="container">
                <div class="current-job-opening-title-sec">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section__title section_title-two text-center">
                                <h2 class="title" data-aos="fade-up" data-aos-delay="100">{{ $join_page->current_job_title ?? 'Current Job Openings' }}</h2>
                            </div>
                            <div class="para-mb text-center" data-aos="fade-up" data-aos-delay="150">
                                {!! $join_page->current_job_description ?? '' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Job role cards — driven by JobRole CRUD --}}
                <div class="current-job-opening-job-card-sec">
                    @forelse($job_roles as $i => $role)
                        <div class="job-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($i * 50) }}">
                            <div>
                                <h4>{{ $role->job_position }}</h4>
                                <div class="job-meta">
                                    @if($role->job_location)
                                        <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-1.webp') }}" alt=""> {{ $role->job_location }}</p>
                                    @endif
                                    @if($role->job_type)
                                        <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-2.webp') }}" alt=""> <i class="bi bi-clock"></i>{{ $role->job_type }}</p>
                                    @endif
                                    @if($role->work_mode)
                                        <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-3.webp') }}" alt=""> {{ $role->work_mode }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="job-actions">
                                <a href="javascript:void(0)" class="btn apply-now-btn" data-bs-toggle="modal"
                                   data-bs-target="#appointmentModal"
                                   data-job-title="{{ $role->job_position }}"
                                   data-job-id="{{ $role->id }}">Apply Now<img
                                        src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></a>
                                @if($role->jd_file)
                                    <a href="{{ asset('home/join-us/jd/'.$role->jd_file) }}" target="_blank"
                                        class="btn btn-job-sec" title="View Job Description"><img
                                            src="{{ asset('frontend/assets/img/icon/pdf-download-icon.webp') }}" alt=""></a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted my-4">No open positions at the moment. Please check back soon.</p>
                    @endforelse
                </div>
                
            </div>
        </section>

        <section class="career-section">
            <div class="container career-inner">

                <div class="row align-items-center g-5">

                    <!-- ── LEFT ── -->
                    <div class="col-lg-5">
                        <div class="career-eyebrow" data-aos="fade-right" data-aos-delay="100">
                            <div class="career-eyebrow-dot">
                                <i class="far fa-eye"></i>
                            </div>
                            <span>{{ $join_page->common_heading ?? "Can't find a suitable role?" }}</span>
                        </div>

                        <div class="section__title">
                            <h2 class="title" data-aos="fade-right" data-aos-delay="150">{{ $join_page->common_title ?? "Don't worry, connect with our team." }}</h2>
                        </div>
                        <div data-aos="fade-right" data-aos-delay="200">
                            {!! $join_page->common_description ?? '' !!}
                        </div>

                        <a href="mailto:careers@sahmumbai.com" target="_blank" class="btn" data-aos="fade-right" data-aos-delay="200">Submit Now<img
                                src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></a>

                    </div>

                    <!-- ── RIGHT ── -->
                    <div class="col-lg-7">
                        <div class="app-box">

                            <!-- Subject Cards -->
                            <div class="subject-cards">

                                @if($join_page && $join_page->commonRows->count())
                                    @foreach($join_page->commonRows as $i => $row)
                                        <div class="scard" data-aos="fade-left" data-aos-delay="{{ 100 + ($i * 50) }}">
                                            <div class="scard-num">
                                                <img src="{{ asset('frontend/assets/img/icon/job-foo-icon.webp') }}" alt="">
                                            </div>
                                            <div class="scard-body">
                                                <div class="scard-role">{{ $row->job_title }}</div>
                                                <div class="scard-tag">
                                                    <span>{{ $row->subject }}</span>
                                                </div>
                                                @if($row->description)
                                                    <div class="scard-desc">{!! $row->description !!}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>



                        </div>
                    </div>

                </div>


            </div>
        </section>

        <section class="join-us-footer-sec"
                 @if($join_page && $join_page->extra_background_image)
                    style="background-image: url('{{ asset('home/join-us/extra/'.$join_page->extra_background_image) }}');"
                 @endif>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="join-us-footer-content-sec" data-aos="fade-right" data-aos-delay="150">
                            {!! $join_page->extra_description ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main>
    <!-- main-area-end -->


    <div class="modal fade book-an-appointment-custom-popup-form-sec" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-header">
                    <h4 class="modal-title">Join Us</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-body">
                    <form id="joinForm" class="appointment-form" action="{{ route('frontend.job_application.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="job_role_id" id="j_job_role_id" value="">

                        <!-- Full Name & Email -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" name="full_name" id="j_full_name" placeholder="Full Name" maxlength="50">
                                    <small class="error-msg" id="jerr_full_name"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="email" class="form-control" name="email" id="j_email" placeholder="Email Address">
                                    <small class="error-msg" id="jerr_email"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Phone & Applying For -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="tel" class="form-control" name="phone" id="j_phone"
                                           placeholder="Phone Number" maxlength="12" inputmode="numeric"
                                           pattern="[0-9]{10,12}"
                                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,12);">
                                    <small class="error-msg" id="jerr_phone"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" name="applying_for" id="j_applying_for" placeholder="Applying For" readonly>
                                    <small class="error-msg" id="jerr_applying_for"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Joining Time -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" name="location" id="j_location" placeholder="Current Location">
                                    <small class="error-msg" id="jerr_location"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <select class="form-control" name="joining_time" id="j_joining_time">
                                        <option value="">How Soon Can You Join Us?</option>
                                        <option value="Immediately">Immediately</option>
                                        <option value="15 Days">Within 15 Days</option>
                                        <option value="30 Days">Within 30 Days</option>
                                        <option value="45 Days">Within 45 Days</option>
                                        <option value="60 Days">Within 60 Days</option>
                                        <option value="90 Days">Within 90 Days</option>
                                    </select>
                                    <small class="error-msg" id="jerr_joining_time"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div class="form-group mb-3">
                            <input type="file" id="resumeUpload" name="resume" accept=".pdf,.doc,.docx" hidden>

                            <label for="resumeUpload" class="form-control upload-resume-btn">
                                Upload Resume (Only .doc & .pdf allowed. Max file size upto 5MB.)
                            </label>

                            <small id="fileName" class="mt-2 d-block"></small>
                            <small class="error-msg" id="jerr_resume"></small>
                        </div>

                        <!-- Message -->
                        <div class="form-group mb-4">
                            <textarea class="form-control" name="message" id="j_message" rows="4" placeholder="Message"></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="bapcpop-btn-sec text-center">
                            <p class="form-status" id="joinStatus"></p>
                            <button type="submit" class="btn submit-btn" id="joinSubmitBtn">
                                Submit Application
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

    <script>
    (function () {
        var modalEl = document.getElementById('appointmentModal');
        var form    = document.getElementById('joinForm');
        if (!modalEl || !form) return;

        var applyingForInput = document.getElementById('j_applying_for');
        var jobRoleInput     = document.getElementById('j_job_role_id');
        var resumeInput      = document.getElementById('resumeUpload');
        var fileLabel        = document.getElementById('fileName');
        var submitBtn        = document.getElementById('joinSubmitBtn');
        var originalBtnHtml  = submitBtn ? submitBtn.innerHTML : '';

        /* -------- Apply Now click: auto-fill the modal -------- */
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.apply-now-btn');
            if (!trigger) return;
            if (applyingForInput) applyingForInput.value = trigger.dataset.jobTitle || '';
            if (jobRoleInput)     jobRoleInput.value     = trigger.dataset.jobId    || '';
        });

        /* -------- Reset the form when the modal closes -------- */
        modalEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            if (fileLabel) fileLabel.textContent = '';
            ['full_name','email','phone','applying_for','location','joining_time','resume','message'].forEach(clearError);
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnHtml; }
        });

        /* -------- Resume file: show selected filename -------- */
        if (resumeInput && fileLabel) {
            resumeInput.addEventListener('change', function () {
                var f = resumeInput.files && resumeInput.files[0];
                if (!f) { fileLabel.textContent = ''; return; }
                var kb = f.size / 1024;
                var size = kb > 1024 ? (kb/1024).toFixed(2)+' MB' : Math.round(kb)+' KB';
                fileLabel.textContent = 'Selected: ' + f.name + ' (' + size + ')';
            });
        }

        function showError(id, msg) {
            var el    = document.getElementById('jerr_' + id);
            var input = form.querySelector('[name="' + id + '"]');
            if (el)    el.textContent = msg;
            if (input) input.classList.add('is-invalid');
        }
        function clearError(id) {
            var el    = document.getElementById('jerr_' + id);
            var input = form.querySelector('[name="' + id + '"]');
            if (el)    el.textContent = '';
            if (input) input.classList.remove('is-invalid');
        }
        // Live-clear on input.
        ['full_name','email','phone','location','joining_time','resume','message'].forEach(function (id) {
            var input = form.querySelector('[name="' + id + '"]');
            if (!input) return;
            input.addEventListener('input',  function () { clearError(id); });
            input.addEventListener('change', function () { clearError(id); });
        });

        form.addEventListener('submit', function (e) {
            ['full_name','email','phone','applying_for','location','joining_time','resume','message'].forEach(clearError);

            var v = function (name) {
                var el = form.querySelector('[name="' + name + '"]');
                return el ? (el.value || '').trim() : '';
            };
            var errors = 0;

            // Full Name
            var name = v('full_name');
            if (name === '') {
                showError('full_name', 'Please enter your name.'); errors++;
            } else if (!/^[A-Za-z\s.'\-]+$/.test(name)) {
                showError('full_name', 'Name cannot contain numbers or special characters.'); errors++;
            }

            // Email — TLD must be letters only (rejects .com1111 etc).
            var email = v('email');
            if (email === '') {
                showError('email', 'Please enter your email address.'); errors++;
            } else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) {
                showError('email', 'Please enter a valid email address.'); errors++;
            }

            // Phone — digits only, 10-12 characters (cap enforced by oninput).
            var phone = v('phone');
            if (phone === '') {
                showError('phone', 'Please enter your phone number.'); errors++;
            } else if (!/^\d{10,12}$/.test(phone)) {
                showError('phone', 'Phone number must be 10 to 12 digits.'); errors++;
            }

            // Applying For (auto-filled — should never be empty on real flow).
            if (v('applying_for') === '') {
                showError('applying_for', 'Please pick a role via Apply Now.'); errors++;
            }

            // Location — required, letters + spaces + basic punctuation only.
            var loc = v('location');
            if (loc === '') {
                showError('location', 'Please enter your current location.'); errors++;
            } else if (!/^[A-Za-z\s.,'\-]+$/.test(loc)) {
                showError('location', 'Location cannot contain numbers or special characters.'); errors++;
            }

            // Joining time
            if (v('joining_time') === '') { showError('joining_time', 'Please pick how soon you can join.'); errors++; }

            // Resume — required, pdf/doc/docx, 5MB.
            var f = resumeInput && resumeInput.files && resumeInput.files[0];
            if (!f) {
                showError('resume', 'Please upload your resume.'); errors++;
            } else {
                var ext = (f.name.split('.').pop() || '').toLowerCase();
                if (['pdf','doc','docx'].indexOf(ext) === -1) {
                    showError('resume', 'Only PDF or Word documents (.pdf, .doc, .docx) are allowed.'); errors++;
                } else if (f.size > 5 * 1024 * 1024) {
                    showError('resume', 'Resume must be 5MB or smaller.'); errors++;
                }
            }
            // Message is optional.

            if (errors > 0) { e.preventDefault(); return false; }

            // Passed — disable button.
            if (submitBtn) {
                submitBtn.disabled  = true;
                submitBtn.innerHTML = 'Submitting…';
            }
            setTimeout(function () {
                if (submitBtn && submitBtn.disabled) {
                    submitBtn.disabled  = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }, 30000);
        });
    })();
    </script>

  </body>
</html>
