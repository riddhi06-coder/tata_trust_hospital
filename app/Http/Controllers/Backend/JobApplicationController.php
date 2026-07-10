<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;

class JobApplicationController extends Controller
{
    public function index()
    {
        $applications = JobApplication::whereNull('deleted_by')
            ->with('jobRole')
            ->orderByDesc('id')
            ->get();

        return view('backend.form_enquiries.career.index', compact('applications'));
    }

    public function show($id)
    {
        $application = JobApplication::whereNull('deleted_by')
            ->with('jobRole')
            ->findOrFail($id);

        return view('backend.form_enquiries.career.show', compact('application'));
    }
}
