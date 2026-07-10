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
use App\Models\BlogListing;
use App\Models\BlogListingSetting;
use App\Models\BlogListingTag;
use App\Mail\ContactEnquiryMail;
use App\Models\ContactDetails;
use App\Models\ContactEnquiry;
use App\Models\JobRole;
use App\Models\JoinPage;
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
                'tags'   => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
                'detail' => fn ($q) => $q->with(['socialLinks' => fn ($s) => $s->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id')]),
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
            'email'     => ['required', 'email', 'max:150'],
            'phone'     => ['required', 'string', 'max:20',
                function ($attr, $value, $fail) {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (strlen($digits) < 10 || strlen($digits) > 12) {
                        $fail('Phone number must be 10 to 12 digits.');
                    }
                },
            ],
            'subject'   => ['required', 'string', 'max:200'],
            'message'   => ['nullable', 'string', 'max:5000'],
        ], [
            'full_name.regex' => 'Name cannot contain numbers or special characters.',
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

    private function sendContactEnquiryMails(ContactEnquiry $enquiry): void
    {
        $adminTo = config('mail.admin_notification');
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
            abort(404);
        }

        return view('frontend.specialities_details', compact('speciality', 'detail'));
    }

}