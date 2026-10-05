<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

/**
 * Backend activity notifications — shared inbox + live polling endpoint.
 * Available to every authenticated admin (not permission-gated; notifications
 * are a core UX element like logout).
 */
class NotificationController extends Controller
{
    /** Full notifications page (AJAX-POST filters, per project convention). */
    public function index(Request $request)
    {
        $notifications = $this->paginatedResults($request);

        $summary = [
            'total'  => AdminNotification::count(),
            'unread' => AdminNotification::unread()->count(),
        ];

        return view('backend.notifications.index', compact('notifications', 'summary'));
    }

    public function filter(Request $request)
    {
        $notifications = $this->paginatedResults($request);

        return view('backend.notifications._table', compact('notifications'))->render();
    }

    private function paginatedResults(Request $request)
    {
        $query = AdminNotification::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('status')) {
            $request->status === 'unread'
                ? $query->whereNull('read_at')
                : $query->whereNotNull('read_at');
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")->orWhere('body', 'like', "%{$s}%"));
        }

        return $query->orderByDesc('id')
            ->paginate(30)
            ->withPath(route('admin.notifications.index'))
            ->withQueryString();
    }

    /**
     * Live polling endpoint — returns the current unread count and the most
     * recent notifications as JSON. Called every ~20s from the header bell.
     */
    public function poll(Request $request)
    {
        $recent = AdminNotification::orderByDesc('id')->limit(12)->get();

        return response()->json([
            'unread' => AdminNotification::unread()->count(),
            'items'  => $recent->map(fn ($n) => [
                'id'     => $n->id,
                'title'  => $n->title,
                'body'   => $n->body,
                'url'    => route('admin.notifications.go', $n->id),
                'icon'   => $n->icon,
                'color'  => $n->color,
                'source' => $n->source,
                'unread' => is_null($n->read_at),
                'ago'    => $n->ago,
            ])->all(),
        ]);
    }

    /** Mark one notification read (JSON). */
    public function read($id)
    {
        $n = AdminNotification::findOrFail($id);
        $n->markRead();

        return response()->json(['ok' => true, 'unread' => AdminNotification::unread()->count()]);
    }

    /** Mark every notification read (JSON). */
    public function readAll()
    {
        AdminNotification::unread()->update([
            'read_at'      => now(),
            'read_by'      => auth()->id(),
            'read_by_name' => optional(auth()->user())->name,
        ]);

        return response()->json(['ok' => true, 'unread' => 0]);
    }

    /** Open a notification: mark read, then redirect to its target. */
    public function go($id)
    {
        $n = AdminNotification::findOrFail($id);
        $n->markRead();

        return redirect($n->url ?: route('admin.notifications.index'));
    }
}
