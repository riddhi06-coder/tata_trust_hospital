<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppBookingRequest;
use Illuminate\Http\Request;

/**
 * Read-only admin listing of appointment-booking intakes captured via the
 * WhatsApp chatbot. Filters are AJAX POST (project convention).
 */
class WhatsAppBookingRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = $this->paginatedResults($request);

        return view('backend.form_enquiries.whatsapp_booking.index', compact('requests'));
    }

    /** AJAX endpoint — returns just the results partial. POST only. */
    public function filter(Request $request)
    {
        $requests = $this->paginatedResults($request);

        return view('backend.form_enquiries.whatsapp_booking._table', compact('requests'))->render();
    }

    private function paginatedResults(Request $request)
    {
        $query = WhatsAppBookingRequest::whereNull('deleted_by');

        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('parent_name', 'like', "%{$s}%")
                  ->orWhere('pet_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('wa_id', 'like', "%{$s}%");
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
            ->withPath(route('manage-whatsapp-bookings.index'))
            ->withQueryString();
    }

    public function show($id)
    {
        $booking = WhatsAppBookingRequest::whereNull('deleted_by')->findOrFail($id);

        return view('backend.form_enquiries.whatsapp_booking.show', compact('booking'));
    }
}
