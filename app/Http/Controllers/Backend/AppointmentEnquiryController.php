<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppointmentEnquiry;

class AppointmentEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = AppointmentEnquiry::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('backend.form_enquiries.appointment.index', compact('enquiries'));
    }

    public function show($id)
    {
        $enquiry = AppointmentEnquiry::whereNull('deleted_by')->findOrFail($id);

        return view('backend.form_enquiries.appointment.show', compact('enquiry'));
    }
}
