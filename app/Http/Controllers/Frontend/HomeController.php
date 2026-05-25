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

        
        return view('frontend.index', compact('banner','short_intro'));
    }

}