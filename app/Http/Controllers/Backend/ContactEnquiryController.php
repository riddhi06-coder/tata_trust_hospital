<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;

class ContactEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = ContactEnquiry::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('backend.form_enquiries.contact.index', compact('enquiries'));
    }

    public function show($id)
    {
        $enquiry = ContactEnquiry::whereNull('deleted_by')->findOrFail($id);

        return view('backend.form_enquiries.contact.show', compact('enquiry'));
    }
}
