<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $logs = $this->paginatedResults($request);

        $types = CommunicationLog::query()->select('type')->distinct()->orderBy('type')->pluck('type')->filter()->values();

        // Quick tallies for the summary strip.
        $summary = [
            'total'  => CommunicationLog::count(),
            'sms'    => CommunicationLog::where('channel', 'sms')->count(),
            'email'  => CommunicationLog::where('channel', 'email')->count(),
            'failed' => CommunicationLog::where('status', 'failed')->count(),
        ];

        return view('backend.communication_logs.index', compact('logs', 'types', 'summary'));
    }

    /**
     * AJAX endpoint — returns just the results partial (table + pagination)
     * so filtering never reloads the page. POST only.
     */
    public function filter(Request $request)
    {
        $this->authorizeSuperAdmin();

        $logs = $this->paginatedResults($request);

        return view('backend.communication_logs._table', compact('logs'))->render();
    }

    private function paginatedResults(Request $request)
    {
        $query = CommunicationLog::query();

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('recipient', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
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
            ->withPath(route('admin.communication-logs.index'))
            ->withQueryString();
    }

    public function show($id)
    {
        $this->authorizeSuperAdmin();

        $log = CommunicationLog::with(['appointmentUser', 'triggeredBy'])->findOrFail($id);

        return view('backend.communication_logs.show', compact('log'));
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(optional(Auth::user())->isSuperAdmin(), 403, 'Only the Super Admin can view the communication log.');
    }
}
