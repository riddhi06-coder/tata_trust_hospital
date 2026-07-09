<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BlogListing;

class BlogListingController extends Controller
{

    public function index()
    {
        return view('backend.blogs.listing.index');
    }

    public function create()
    {
        return view('backend.blogs.listing.create');
    }

}