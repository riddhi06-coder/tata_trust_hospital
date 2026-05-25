<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\HomeBanner;
use App\Models\HomeBoard;
use App\Models\HomeFacilities;
use App\Models\HomeFollowUs;
use App\Models\HomeSpecialities;
use App\Models\HomeTeam;
use App\Models\HomeTestimonials;
use App\Models\OurTeam;
use App\Models\ShortIntroduction;
use App\Models\Testimonials;




class HomeController extends Controller
{

    // Home Page
    public function index()
    {
        $banner = HomeBanner::wherenull('deleted_by')->orderBy('created_at', 'asc')->get();
        $short_intro = ShortIntroduction::wherenull('deleted_by')->first();
        $specialities = HomeSpecialities::wherenull('deleted_by')->first();
        $facilities = HomeFacilities::wherenull('deleted_by')->first();
        $our_team = HomeTeam::wherenull('deleted_by')->first();
        $team_members = OurTeam::wherenull('deleted_by')->orderBy('created_at', 'asc')->where('show_on_home', '1')->get();

        $testimonial_details = HomeTestimonials::wherenull('deleted_by')->first();
        $testimonials = Testimonials::wherenull('deleted_by')->orderBy('created_at', 'asc')->get();

        $our_board = HomeBoard::wherenull('deleted_by')->first();
        $follow_us = HomeFollowUs::wherenull('deleted_by')->first();

        return view('frontend.index', compact('banner','short_intro','specialities','facilities','our_team','team_members','testimonial_details','testimonials','our_board','follow_us'));
    }

}