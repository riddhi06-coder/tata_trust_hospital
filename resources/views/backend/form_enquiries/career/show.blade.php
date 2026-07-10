<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $initials = collect(explode(' ', trim($application->full_name)))
            ->filter()
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->implode('');
        $resumeUrl  = $application->resume_file ? asset('home/careers/resumes/'.$application->resume_file) : null;
        $resumeExt  = $application->resume_file ? strtolower(pathinfo($application->resume_file, PATHINFO_EXTENSION)) : '';
        $resumeSize = null;
        if ($application->resume_file) {
            $abs = public_path('home/careers/resumes/'.$application->resume_file);
            if (file_exists($abs)) {
                $bytes = filesize($abs);
                $resumeSize = $bytes >= 1048576
                    ? number_format($bytes / 1048576, 2).' MB'
                    : number_format(max(1, round($bytes / 1024))).' KB';
            }
        }
    @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>Job Application</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-job-applications.index') }}">Job Applications</a></li>
                            <li class="breadcrumb-item active">#{{ $application->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid enq-page">

            {{-- Header --}}
            <div class="enq-header">
                <div class="enq-avatar">{{ $initials ?: '?' }}</div>
                <div class="enq-header-meta">
                    <h1>{{ $application->full_name }}</h1>
                    <p class="enq-header-sub">
                        <span>{{ $application->applying_for }}</span>
                        <span class="enq-sub-dot">·</span>
                        <span>{{ optional($application->created_at)->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
                <div class="enq-header-ref">
                    #{{ str_pad($application->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="row g-3">
                {{-- LEFT: Details --}}
                <div class="col-lg-8">

                    <div class="enq-card">
                        <div class="enq-card-title">Applicant Information</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Full Name</span>
                                <div class="enq-value">{{ $application->full_name }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Email Address</span>
                                <div class="enq-value"><a href="mailto:{{ $application->email }}">{{ $application->email }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Phone Number</span>
                                <div class="enq-value"><a href="tel:{{ preg_replace('/[^\d+]/', '', $application->phone) }}">{{ $application->phone }}</a></div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Current Location</span>
                                <div class="enq-value">{{ $application->location ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Application Details</div>
                        <div class="enq-field-grid">
                            <div class="enq-field">
                                <span class="enq-label">Applying For</span>
                                <div class="enq-value">{{ $application->applying_for }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Linked Job Role</span>
                                <div class="enq-value">
                                    @if($application->jobRole)
                                        <a href="{{ route('manage-job-role.edit', $application->jobRole->id) }}">{{ $application->jobRole->job_position }}</a>
                                    @else
                                        <span class="enq-muted">— (role removed)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Can Join In</span>
                                <div class="enq-value">{{ $application->joining_time ?? '—' }}</div>
                            </div>
                            <div class="enq-field">
                                <span class="enq-label">Submitted</span>
                                <div class="enq-value">{{ optional($application->created_at)->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($application->message)
                        <div class="enq-card">
                            <div class="enq-card-title">Applicant Message</div>
                            <p class="enq-message-body">{{ $application->message }}</p>
                        </div>
                    @endif

                </div>

                {{-- RIGHT: Resume, actions, metadata --}}
                <div class="col-lg-4">

                    @if($resumeUrl)
                        <div class="enq-card">
                            <div class="enq-card-title">Resume</div>
                            <div class="enq-resume-preview">
                                <div class="enq-resume-icon">
                                    <i class="fa fa-file-{{ $resumeExt === 'pdf' ? 'pdf-o' : 'word-o' }}"></i>
                                </div>
                                <div class="enq-resume-info">
                                    <div class="fname">{{ $application->resume_file }}</div>
                                    <div class="fmeta">{{ strtoupper($resumeExt) }}{{ $resumeSize ? ' · '.$resumeSize : '' }}</div>
                                </div>
                            </div>
                            <div class="enq-actions">
                                <a href="{{ $resumeUrl }}" target="_blank" class="btn btn-primary w-100">Download Resume</a>
                            </div>
                        </div>
                    @endif

                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="mailto:{{ $application->email }}?subject=Re: Application for {{ urlencode($application->applying_for) }}" class="btn btn-primary">Reply via Email</a>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $application->phone) }}" class="btn btn-outline-secondary">Call Applicant</a>
                            <a href="{{ route('manage-job-applications.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Submission Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">Application ID</span><span class="v">#{{ str_pad($application->id, 4, '0', STR_PAD_LEFT) }}</span></li>
                            <li><span class="k">Submitted</span><span class="v">{{ optional($application->created_at)->format('d M Y, h:i A') }}</span></li>
                            @if($application->ip_address)
                                <li><span class="k">IP Address</span><span class="v enq-mono">{{ $application->ip_address }}</span></li>
                            @endif
                            @if($application->user_agent)
                                <li>
                                    <span class="k">User Agent</span>
                                    <span class="v enq-mono" title="{{ $application->user_agent }}">{{ \Illuminate\Support\Str::limit($application->user_agent, 32) }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
