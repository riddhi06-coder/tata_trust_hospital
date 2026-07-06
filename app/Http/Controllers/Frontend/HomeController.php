<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\EventSetting;
use App\Models\Events;
use App\Models\AboutUs;
use App\Models\FacilitySetting;
use App\Models\Faq;
use App\Models\FaqSetting;
use App\Models\MasterFacility;
use App\Models\Gallery;
use App\Models\GalleryImage;
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