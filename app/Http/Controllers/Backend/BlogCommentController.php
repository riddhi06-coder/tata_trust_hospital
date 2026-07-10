<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Models\BlogListing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCommentController extends Controller
{
    public function index(Request $request)
    {
        $blogFilter = $request->input('blog');

        $query = BlogComment::whereNull('deleted_by')
            ->with(['blogListing' => fn ($q) => $q->withTrashed()])
            ->orderByDesc('id');

        if ($blogFilter) {
            $query->where('blog_listing_id', $blogFilter);
        }

        $comments = $query->get();

        // Blog dropdown — only listings that have at least one live comment.
        $blogs = BlogListing::whereNull('deleted_by')
            ->whereHas('comments', fn ($q) => $q->whereNull('deleted_by'))
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('backend.blogs.comments.index', compact('comments', 'blogs', 'blogFilter'));
    }

    public function toggleActive($id)
    {
        $comment = BlogComment::whereNull('deleted_by')->findOrFail($id);
        $comment->update([
            'is_active'  => ! $comment->is_active,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->back()
            ->with('message', $comment->is_active
                ? 'Comment is now live on the site.'
                : 'Comment hidden from the site.');
    }

    public function destroy($id)
    {
        try {
            $comment = BlogComment::whereNull('deleted_by')->findOrFail($id);
            $comment->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-blog-comments.index')
                ->with('message', 'Comment deleted.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }
}
