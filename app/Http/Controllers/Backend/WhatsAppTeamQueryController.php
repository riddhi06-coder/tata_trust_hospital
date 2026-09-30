<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTeamQuery;
use Illuminate\Http\Request;

/**
 * Read-only admin listing of "Talk to our team" messages captured via the
 * WhatsApp chatbot. Filters are AJAX POST (project convention).
 */
class WhatsAppTeamQueryController extends Controller
{
    public function index(Request $request)
    {
        $queries = $this->paginatedResults($request);

        return view('backend.form_enquiries.whatsapp_query.index', compact('queries'));
    }

    /** AJAX endpoint — returns just the results partial. POST only. */
    public function filter(Request $request)
    {
        $queries = $this->paginatedResults($request);

        return view('backend.form_enquiries.whatsapp_query._table', compact('queries'))->render();
    }

    private function paginatedResults(Request $request)
    {
        $query = WhatsAppTeamQuery::whereNull('deleted_by');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('wa_id', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
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
            ->withPath(route('manage-whatsapp-queries.index'))
            ->withQueryString();
    }

    public function show($id)
    {
        $query = WhatsAppTeamQuery::whereNull('deleted_by')->findOrFail($id);

        return view('backend.form_enquiries.whatsapp_query.show', compact('query'));
    }
}
