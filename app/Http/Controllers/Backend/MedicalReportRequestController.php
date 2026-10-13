<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MedicalReportRequest;
use Illuminate\Http\Request;

/**
 * Read-only admin listing of medical-report requests captured via the WhatsApp
 * chatbot. Filters are AJAX POST (project convention).
 */
class MedicalReportRequestController extends Controller
{
    public function index(Request $request)
    {
        $reports = $this->paginatedResults($request);

        return view('backend.form_enquiries.medical_report.index', compact('reports'));
    }

    /** AJAX endpoint — returns just the results partial. POST only. */
    public function filter(Request $request)
    {
        $reports = $this->paginatedResults($request);

        return view('backend.form_enquiries.medical_report._table', compact('reports'))->render();
    }

    private function paginatedResults(Request $request)
    {
        $query = MedicalReportRequest::whereNull('deleted_by');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('client_name', 'like', "%{$s}%")
                  ->orWhere('patient_name', 'like', "%{$s}%")
                  ->orWhere('patient_uid', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('wa_id', 'like', "%{$s}%")
                  ->orWhere('report_name', 'like', "%{$s}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->orderByDesc('id')
            ->paginate(30)
            ->withPath(route('manage-medical-reports.index'))
            ->withQueryString();
    }

    public function show($id)
    {
        $report = MedicalReportRequest::whereNull('deleted_by')->findOrFail($id);

        return view('backend.form_enquiries.medical_report.show', compact('report'));
    }
}
