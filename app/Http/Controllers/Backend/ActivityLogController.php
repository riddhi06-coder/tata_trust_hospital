<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $query = ActivityLog::query()->with('user');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                    ->orWhere('user_name', 'like', "%{$s}%")
                    ->orWhere('auditable_id', 'like', "%{$s}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('id')->paginate(30)->withQueryString();

        $users   = User::orderBy('name')->get(['id', 'name']);
        $modules = ActivityLog::query()->select('module')->distinct()->orderBy('module')->pluck('module')->filter()->values();
        $events  = ActivityLog::query()->select('event')->distinct()->orderBy('event')->pluck('event')->filter()->values();

        return view('backend.activity_logs.index', compact('logs', 'users', 'modules', 'events'));
    }

    public function show($id)
    {
        $this->authorizeSuperAdmin();

        $log = ActivityLog::with('user')->findOrFail($id);

        // id => name map so "…_by" fields can be shown as a person, not a number.
        $users = User::withTrashed()->pluck('name', 'id');

        return view('backend.activity_logs.show', compact('log', 'users'));
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(optional(Auth::user())->isSuperAdmin(), 403, 'Only the Super Admin can view the activity log.');
    }
}
