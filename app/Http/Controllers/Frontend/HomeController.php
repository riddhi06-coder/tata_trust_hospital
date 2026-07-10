<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Models\EventSetting;
use App\Models\Events;
use App\Models\AboutUs;
use App\Models\FacilitySetting;
use App\Models\Faq;
use App\Models\FaqSetting;
use App\Models\MasterFacility;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogListing;
use App\Models\BlogListingSetting;
use App\Models\BlogListingTag;
use App\Mail\AppointmentEnquiryMail;
use App\Mail\ContactEnquiryMail;
use App\Mail\JobApplicationMail;
use App\Models\AppointmentEnquiry;
use App\Models\AppointmentOtp;
use App\Models\AppointmentUser;
use App\Models\ContactDetails;
use App\Models\ContactEnquiry;
use App\Models\JobApplication;
use App\Models\JobRole;
use App\Models\JoinPage;
use App\Services\MessageIndiaSms;
use App\Models\Specialities;
use App\Models\SpecialitiesDetails;
use App\Models\SpecialitySetting;
use App\Models\HomeBanner;
use App\Models\HomeBoard;
use App\Models\HomeFacilities;
use App\Models\HomeFollowUs;
use App\Models\HomeServices;
use App\Models\HomeTeam;
use App\Models\HomeTestimonials;
use App\Models\OurTeam;
use App\Models\OurTeamSetting;
use App\Models\ShortIntroduction;
use App\Models\Testimonials;




class HomeController extends Controller
{

    // Home Page
    public function index()
    {
        $banner = HomeBanner::wherenull('deleted_by')->orderBy('created_at', 'asc')->get();
        $short_intro = ShortIntroduction::wherenull('deleted_by')->first();
        $specialities = HomeServices::wherenull('deleted_by')->first();
        $facilities = HomeFacilities::wherenull('deleted_by')->first();
        $our_team = HomeTeam::wherenull('deleted_by')->first();
        $team_members = OurTeam::wherenull('deleted_by')->orderBy('created_at', 'asc')->where('show_on_home', '1')->get();

        $testimonial_details = HomeTestimonials::wherenull('deleted_by')->first();
        $testimonials = Testimonials::wherenull('deleted_by')->orderBy('created_at', 'asc')->get();

        $our_board = HomeBoard::wherenull('deleted_by')->first();
        $follow_us = HomeFollowUs::wherenull('deleted_by')->first();

        $gallery_settings = Gallery::whereNull('deleted_by')->first();
        $gallery_images   = GalleryImage::whereNull('deleted_by')
            ->where('show_on_home', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $event_settings = EventSetting::whereNull('deleted_by')->first();
        $events         = Events::whereNull('deleted_by')
            ->where('show_on_home', true)
            ->orderBy('id')
            ->get();

        $speciality_settings = SpecialitySetting::whereNull('deleted_by')->first();
        $speciality_items    = Specialities::whereNull('deleted_by')
            ->withCount(['details' => fn ($q) => $q->whereNull('deleted_by')])
            ->orderBy('id')
            ->get();

        return view('frontend.index', compact('banner','short_intro','specialities','facilities','our_team','team_members','testimonial_details','testimonials','our_board','follow_us','gallery_settings','gallery_images','event_settings','events','speciality_settings','speciality_items'));
    }



    public function gallery()
    {
        $gallery_settings = Gallery::whereNull('deleted_by')->first();
        $gallery_images   = GalleryImage::whereNull('deleted_by')
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.gallery', compact('gallery_settings', 'gallery_images'));
    }

    public function specialities()
    {
        $speciality_settings = SpecialitySetting::whereNull('deleted_by')->first();
        $speciality_items    = Specialities::whereNull('deleted_by')
            ->withCount(['details' => fn ($q) => $q->whereNull('deleted_by')])
            ->orderBy('id')
            ->get();

        // "Our Services" tabbed section comes from the HomeServices CRUD
        $home_services = HomeServices::whereNull('deleted_by')->first();

        return view('frontend.specialities', compact('speciality_settings', 'speciality_items', 'home_services'));
    }

    public function our_facilities()
    {
        $facility_settings = FacilitySetting::whereNull('deleted_by')->first();
        $facilities        = MasterFacility::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('frontend.our_facilities', compact('facility_settings', 'facilities'));
    }

    public function about_us()
    {
        $about = AboutUs::whereNull('deleted_by')->first();

        return view('frontend.about_us', compact('about'));
    }

    public function our_team()
    {
        $team_settings = OurTeamSetting::whereNull('deleted_by')->first();

        $team_members  = OurTeam::whereNull('deleted_by')
            ->where('show_on_team_page', true)
            ->orderBy('name')
            ->get();

        return view('frontend.our_team', compact('team_settings', 'team_members'));
    }

    public function faqs()
    {
        $faq_settings = FaqSetting::whereNull('deleted_by')->first();
        $faqs         = Faq::whereNull('deleted_by')->orderBy('id')->get();

        return view('frontend.faqs', compact('faq_settings', 'faqs'));
    }

    public function join_us()
    {
        $join_page = JoinPage::whereNull('deleted_by')
            ->with([
                'infos'      => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
                'commonRows' => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
            ])
            ->first();

        $job_roles = JobRole::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('frontend.join_us', compact('join_page', 'job_roles'));
    }

    public function blogs(Request $request)
    {
        $search   = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $tag      = trim((string) $request->input('tag', ''));

        $query = BlogListing::whereNull('deleted_by')
            ->with([
                'category',
                'tags' => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
            ]);

        if ($category !== '') {
            $query->whereHas('category', fn ($c) => $c->whereNull('deleted_by')->where('slug', $category));
        }

        if ($tag !== '') {
            $query->whereHas('tags', fn ($t) => $t->whereNull('deleted_by')->where('tag', $tag));
        }

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('tags', fn ($t) => $t->whereNull('deleted_by')->where('tag', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn ($c) => $c->whereNull('deleted_by')->where('name', 'like', "%{$search}%"));
            });
        }

        $listings = $query->orderByDesc('blog_date')->orderByDesc('id')->get();

        // AJAX response: just the cards partial.
        if ($request->ajax() || $request->has('partial')) {
            return view('frontend.partials.blog_cards', compact('listings'))->render();
        }

        $settings = BlogListingSetting::whereNull('deleted_by')->first();

        $categories = BlogCategory::whereNull('deleted_by')
            ->withCount(['listings' => fn ($q) => $q->whereNull('deleted_by')])
            ->orderBy('name')
            ->get();

        // Recent posts always reflect the newest 4 blogs regardless of active filter.
        $recentPosts = BlogListing::whereNull('deleted_by')
            ->orderByDesc('blog_date')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $tags = BlogListingTag::whereNull('deleted_by')
            ->select('tag')
            ->distinct()
            ->orderBy('tag')
            ->pluck('tag');

        return view('frontend.blogs', compact(
            'settings', 'listings', 'categories', 'recentPosts', 'tags',
            'search', 'category', 'tag'
        ));
    }

    public function blog_details(string $slug)
    {
        $listing = BlogListing::whereNull('deleted_by')
            ->where('slug', $slug)
            ->with([
                'category',
                'tags'     => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
                'detail'   => fn ($q) => $q->with(['socialLinks' => fn ($s) => $s->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id')]),
                'comments' => fn ($q) => $q->whereNull('deleted_by')->where('is_active', true)->orderByDesc('created_at'),
            ])
            ->firstOrFail();

        $settings = BlogListingSetting::whereNull('deleted_by')->first();

        $recentPosts = BlogListing::whereNull('deleted_by')
            ->where('id', '!=', $listing->id)
            ->orderByDesc('blog_date')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $categories = BlogCategory::whereNull('deleted_by')
            ->withCount(['listings' => fn ($q) => $q->whereNull('deleted_by')])
            ->orderBy('name')
            ->get();

        $tags = BlogListingTag::whereNull('deleted_by')
            ->select('tag')
            ->distinct()
            ->orderBy('tag')
            ->pluck('tag');

        return view('frontend.blog_details', compact('listing', 'settings', 'recentPosts', 'categories', 'tags'));
    }

    public function blog_comment_store(Request $request, string $slug)
    {
        $isAjax = $request->ajax() || $request->wantsJson();

        // Honeypot: silently succeed to fool bots without polluting the DB.
        if (! empty($request->input('website_url'))) {
            return $isAjax
                ? response()->json(['success' => true, 'silent' => true])
                : redirect()->route('frontend.blog_details', $slug);
        }

        $listing = BlogListing::whereNull('deleted_by')->where('slug', $slug)->firstOrFail();

        Validator::make($request->all(), [
            'name'    => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'email'   => ['required', 'email', 'max:150', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/'],
            'website' => ['nullable', 'url', 'max:255'],
            'comment' => ['required', 'string', 'min:3', 'max:5000'],
        ], [
            'name.regex'    => 'Name cannot contain numbers or special characters.',
            'email.regex'   => 'Please enter a valid email address.',
            'comment.min'   => 'Comment must be at least 3 characters.',
        ])->validate();

        $comment = BlogComment::create([
            'blog_listing_id' => $listing->id,
            'name'            => $request->name,
            'email'           => $request->email,
            'website'         => $request->website,
            'comment'         => $request->comment,
            'is_active'       => true,
            'ip_address'      => $request->ip(),
            'user_agent'      => (string) $request->userAgent(),
            'created_at'      => Carbon::now(),
        ]);

        if ($isAjax) {
            $totalComments = $listing->comments()->whereNull('deleted_by')->where('is_active', true)->count();
            return response()->json([
                'success' => true,
                'count'   => $totalComments,
                'html'    => view('frontend.partials.blog_comment', ['c' => $comment])->render(),
            ]);
        }

        return redirect()
            ->route('frontend.blog_details', $slug)
            ->with('comment_posted', true)
            ->withFragment('comments');
    }

    public function contact_us()
    {
        $contact = ContactDetails::whereNull('deleted_by')
            ->with([
                'ribbonItems' => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
            ])
            ->first();

        return view('frontend.contact_us', compact('contact'));
    }

    public function contact_enquiry_store(Request $request)
    {
        // Server-side mirror of the JS validation.
        Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'email'     => ['required', 'email', 'max:150', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/'],
            'phone'     => ['required', 'regex:/^\d{10,12}$/'],
            'subject'   => ['required', 'string', 'max:200'],
            'message'   => ['nullable', 'string', 'max:5000'],
        ], [
            'full_name.regex' => 'Name cannot contain numbers or special characters.',
            'email.regex'     => 'Please enter a valid email address.',
            'phone.regex'     => 'Phone number must be 10 to 12 digits.',
        ])->validate();

        $enquiry = ContactEnquiry::create([
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'created_at' => Carbon::now(),
        ]);

        // Fire the two mails. Wrapped so a failing mail server never blocks the enquiry.
        $this->sendContactEnquiryMails($enquiry);

        return redirect()
            ->route('frontend.thank_you')
            ->with('enquiry_name', $enquiry->full_name);
    }

    public function thank_you()
    {
        $name = session('enquiry_name');
        // Guard: hitting /thank-you directly without a submission bounces to home.
        if (! $name) {
            return redirect()->route('frontend.contact_us');
        }
        return view('frontend.thank_you', compact('name'));
    }

    public function job_application_store(Request $request)
    {
        Validator::make($request->all(), [
            'full_name'    => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'email'        => ['required', 'email', 'max:150', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/'],
            'phone'        => ['required', 'regex:/^\d{10,12}$/'],
            'applying_for' => ['required', 'string', 'max:255'],
            'job_role_id'  => ['nullable', 'integer', 'exists:job_roles,id'],
            'location'     => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.,\'-]+$/'],
            'joining_time' => ['required', 'string', 'max:100'],
            'message'      => ['nullable', 'string', 'max:5000'],
            'resume'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'full_name.regex'   => 'Name cannot contain numbers or special characters.',
            'email.regex'       => 'Please enter a valid email address.',
            'phone.regex'       => 'Phone number must be 10 to 12 digits.',
            'location.regex'    => 'Location cannot contain numbers or special characters.',
            'resume.mimes'      => 'Resume must be a PDF or Word document (.pdf, .doc, .docx).',
            'resume.max'        => 'Resume must be 5MB or smaller.',
        ])->validate();

        // Store the resume under public/home/careers/resumes/ (matches project's public-uploads pattern).
        $folder = public_path('home/careers/resumes');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        $file = $request->file('resume');
        // Sanitize the original name into a URL-safe slug:
        //   "Riddhi Bhosale - 2024.docx"  →  "Riddhi-Bhosale-2024"
        $safeBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = preg_replace('/\s+/', '-', $safeBase);            // spaces/tabs → hyphen
        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '-', $safeBase); // other specials → hyphen
        $safeBase = preg_replace('/-+/', '-', $safeBase);              // collapse repeats
        $safeBase = trim($safeBase, '-._');
        if ($safeBase === '') { $safeBase = 'resume'; }
        $storedName = $safeBase.'-'.time().'-'.uniqid().'.'.strtolower($file->getClientOriginalExtension());
        $file->move($folder, $storedName);

        $application = JobApplication::create([
            'job_role_id'  => $request->job_role_id,
            'applying_for' => $request->applying_for,
            'full_name'    => $request->full_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'location'     => $request->location,
            'joining_time' => $request->joining_time,
            'message'      => $request->message,
            'resume_file'  => $storedName,
            'ip_address'   => $request->ip(),
            'user_agent'   => (string) $request->userAgent(),
            'created_at'   => Carbon::now(),
        ]);

        $this->sendJobApplicationMails($application, $folder.'/'.$storedName, $file->getClientOriginalName());

        return redirect()
            ->route('frontend.join_thank_you')
            ->with('applicant_name', $application->full_name);
    }

    public function join_thank_you()
    {
        $name = session('applicant_name');
        // if (! $name) {
        //     return redirect()->route('frontend.join_us');
        // }
        return view('frontend.join_thank_you', compact('name'));
    }

    private function sendJobApplicationMails(JobApplication $application, string $resumeAbsolutePath, string $resumeOriginalName): void
    {
        $adminTo  = config('mail.admin_notifications.career');
        $hasLogo  = file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'));

        // Look up the JobRole for the JD attachment (if the role has one uploaded).
        $jdPath = null;
        $jdName = null;
        if ($application->job_role_id) {
            $role = JobRole::whereNull('deleted_by')->find($application->job_role_id);
            if ($role && $role->jd_file) {
                $candidate = public_path('home/join-us/jd/'.$role->jd_file);
                if (file_exists($candidate)) {
                    $jdPath = $candidate;
                    // Use a friendly filename for the recipient.
                    $jdName = 'JD-'.preg_replace('/[^A-Za-z0-9._-]/', '_', $application->applying_for).'.'.pathinfo($role->jd_file, PATHINFO_EXTENSION);
                }
            }
        }

        // Admin/HR mail — includes candidate resume + JD reference.
        try {
            if ($adminTo) {
                Mail::to($adminTo)->send(new JobApplicationMail(
                    $application,
                    'New Job Application',
                    'A new application has been submitted through the careers page. Details below:',
                    $jdPath ? 'Resume is attached. Job description for this role is attached as reference.'
                            : 'Resume is attached.',
                    $hasLogo,
                    true,             // attach resume
                    $resumeAbsolutePath,
                    $resumeOriginalName,
                    $jdPath,
                    $jdName,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Job application admin mail failed: '.$e->getMessage(), ['application_id' => $application->id]);
        }

        // Applicant confirmation — includes JD, no resume attachment.
        try {
            Mail::to($application->email)->send(new JobApplicationMail(
                $application,
                'Application Received',
                'Thank you for applying for the <b>'.e($application->applying_for).'</b> role at Tata Trusts Small Animal Hospital. We have received your application and our team will review it. Here is a copy of what you submitted:',
                $jdPath ? 'The job description is attached for your reference.' : '',
                $hasLogo,
                false,            // don't send applicant their own resume back
                null,
                null,
                $jdPath,
                $jdName,
            ));
        } catch (\Throwable $e) {
            Log::error('Job application user mail failed: '.$e->getMessage(), ['application_id' => $application->id]);
        }
    }

    private function sendContactEnquiryMails(ContactEnquiry $enquiry): void
    {
        $adminTo = config('mail.admin_notifications.contact', config('mail.admin_notification'));
        $hasLogo = file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'));

        // Admin notification.
        try {
            if ($adminTo) {
                Mail::to($adminTo)->send(new ContactEnquiryMail(
                    $enquiry,
                    'New Contact Enquiry',
                    'You have received a new contact enquiry from the website. Details are below.',
                    '',
                    $hasLogo
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Contact enquiry admin mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }

        // User confirmation.
        try {
            Mail::to($enquiry->email)->send(new ContactEnquiryMail(
                $enquiry,
                'Thanks for reaching out',
                'Hi '.e($enquiry->full_name).', we\'ve received your enquiry and our team will get back to you shortly. ',
                'This is an automated confirmation. Our team will contact you shortly.',
                $hasLogo
            ));
        } catch (\Throwable $e) {
            Log::error('Contact enquiry user mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }
    }

    public function specialities_details(string $slug)
    {
        $speciality = Specialities::whereNull('deleted_by')->where('slug', $slug)->firstOrFail();

        $detail = SpecialitiesDetails::whereNull('deleted_by')
            ->with('doctors')
            ->where('speciality_id', $speciality->id)
            ->orderBy('id')
            ->first();

        if (!$detail) {
            return redirect()->route('frontend.coming_soon');
        }

        return view('frontend.specialities_details', compact('speciality', 'detail'));
    }


    public function user_login()
    {
        return view('frontend.user_login');
    }

    public function send_otp(Request $request, MessageIndiaSms $sms)
    {
        $mobile = preg_replace('/\D/', '', (string) $request->input('mobile', ''));

        if (strlen($mobile) !== 10) {
            return response()->json([
                'success' => false,
                'message' => 'Enter a valid 10-digit mobile number.',
            ], 422);
        }

        // Rate-limit: max 4 sends in a 10-minute window per mobile.
        $recent = AppointmentOtp::where('mobile', $mobile)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->count();
        if ($recent >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please wait a few minutes.',
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);

        // Clear any prior unverified OTPs for this mobile — one active code at a time.
        AppointmentOtp::where('mobile', $mobile)->delete();

        AppointmentOtp::create([
            'mobile'     => $mobile,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(3),
            'attempts'   => 0,
        ]);

        $sent = $sms->sendOtp($mobile, $otp);

        // If SMS delivery failed we still let the user try (record was created);
        // response tells the frontend so it can show an appropriate message.
        return response()->json([
            'success' => true,
            'delivered' => $sent,
            'mobile'  => $mobile,
            'message' => $sent
                ? 'OTP sent to your mobile.'
                : 'Could not send SMS right now. Please try Resend in a moment.',
        ]);
    }

    public function verify_otp(Request $request)
    {
        $mobile = preg_replace('/\D/', '', (string) $request->input('mobile', ''));
        $otp    = preg_replace('/\D/', '', (string) $request->input('otp', ''));

        if (strlen($mobile) !== 10 || strlen($otp) !== 6) {
            return response()->json([
                'success' => false,
                'message' => 'Enter the 6-digit OTP.',
            ], 422);
        }

        $record = AppointmentOtp::where('mobile', $mobile)
            ->where('otp', $otp)
            ->first();

        if (! $record) {
            // Bump attempts on the latest OTP for this mobile (rate-limit brute-force).
            AppointmentOtp::where('mobile', $mobile)
                ->orderByDesc('id')
                ->limit(1)
                ->update(['attempts' => DB::raw('attempts + 1')]);

            return response()->json([
                'success' => false,
                'message' => 'Incorrect OTP. Please try again.',
            ], 422);
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please resend.',
            ], 422);
        }

        // Success — burn all OTPs for this mobile so it can't be reused.
        AppointmentOtp::where('mobile', $mobile)->delete();

        // Create or find the user row (name/email get added later on form submit).
        $user = AppointmentUser::firstOrCreate(
            ['mobile' => $mobile],
            ['created_at' => Carbon::now()]
        );
        $user->update(['last_verified_at' => Carbon::now()]);

        session([
            'appointment_verified_mobile' => $mobile,
            'appointment_user_id'         => $user->id,
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => route('frontend.book_an_appointment'),
        ]);
    }

    public function book_an_appointment()
    {
        $mobile = session('appointment_verified_mobile');
        if (! $mobile) {
            return redirect()
                ->route('frontend.user_login')
                ->with('info', 'Please verify your mobile number first.');
        }

        $user = AppointmentUser::whereNull('deleted_by')
            ->where('mobile', $mobile)
            ->first();

        return view('frontend.book_an_appointment', compact('mobile', 'user'));
    }

    public function appointment_store(Request $request, MessageIndiaSms $sms)
    {
        // Must have completed OTP verification in this session.
        $mobile = session('appointment_verified_mobile');
        $userId = session('appointment_user_id');

        if (! $mobile || ! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please verify your mobile number again.',
                'redirect' => route('frontend.user_login'),
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'             => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'email'            => ['required', 'email', 'max:150', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/'],
            'address'          => ['required', 'string', 'max:255'],
            'pincode'          => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'pet_name'         => ['required', 'string', 'max:100'],
            'pet_age'          => ['nullable', 'string', 'max:60'],
            'pet_type'         => ['required', 'in:dog,cat'],
            'pet_gender'       => ['required', 'in:male,female'],
            'consult_type'     => ['required', 'in:first,followup'],
            'reason'           => ['required', 'string', 'max:2000'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'name.regex'                 => 'Name cannot contain numbers or special characters.',
            'email.regex'                => 'Please enter a valid email address.',
            'pincode.regex'              => 'Enter a valid 6-digit PIN code.',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the highlighted fields.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = AppointmentUser::whereNull('deleted_by')->where('mobile', $mobile)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please verify your mobile number again.',
                'redirect' => route('frontend.user_login'),
            ], 401);
        }

        // Update the profile fields from this submission.
        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'address' => $request->address,
            'pincode' => $request->pincode,
        ]);

        $enquiry = AppointmentEnquiry::create([
            'appointment_user_id' => $user->id,
            'owner_name'          => $request->name,
            'mobile'              => $mobile,
            'email'               => $request->email,
            'address'             => $request->address,
            'pincode'             => $request->pincode,
            'pet_name'            => $request->pet_name,
            'pet_age'             => $request->pet_age,
            'pet_type'            => $request->pet_type,
            'pet_gender'          => $request->pet_gender,
            'consult_type'        => $request->consult_type,
            'reason'              => $request->reason,
            'appointment_date'    => $request->appointment_date,
        ]);

        // Fire SMS + emails. Failures are logged but never block the response.
        $this->sendAppointmentNotifications($enquiry, $sms);

        return response()->json([
            'success'  => true,
            'redirect' => route('frontend.appointment_thank_you'),
        ]);
    }

    public function appointment_thank_you()
    {
        return view('frontend.appointment_thank_you');
    }

    private function sendAppointmentNotifications(AppointmentEnquiry $enquiry, MessageIndiaSms $sms): void
    {
        $formattedDate = $enquiry->appointment_date->format('d M Y');

        // Tentative-booking SMS.
        try {
            $sms->sendAppointmentConfirmation($enquiry->mobile, $formattedDate);
        } catch (\Throwable $e) {
            Log::error('Appointment SMS failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }

        $adminTo = config('mail.admin_notifications.appointment', config('mail.admin_notification'));
        $hasLogo = file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'));

        // Owner acknowledgement.
        try {
            Mail::to($enquiry->email)->send(new AppointmentEnquiryMail(
                $enquiry,
                'Appointment Request Received - SAHM',
                'Thank you, '.e($enquiry->owner_name).'!',
                'We have received your appointment request. Our team will contact you shortly to confirm your visit. Details of your booking are below.',
                'Please note: this is a tentative booking. Our Customer Care Department will call you to confirm the date and time.',
                $hasLogo
            ));
        } catch (\Throwable $e) {
            Log::error('Appointment owner mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }

        // Admin notification.
        try {
            if ($adminTo) {
                Mail::to($adminTo)
                    ->send((new AppointmentEnquiryMail(
                        $enquiry,
                        'New Appointment Enquiry - '.$enquiry->owner_name,
                        'New Appointment Enquiry',
                        'A new appointment request has been submitted through the website. Details are below.',
                        '',
                        $hasLogo
                    ))->replyTo($enquiry->email, $enquiry->owner_name));
            }
        } catch (\Throwable $e) {
            Log::error('Appointment admin mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }
    }


    public function coming_soon()
    {
        return view('frontend.coming_soon');
    }
}