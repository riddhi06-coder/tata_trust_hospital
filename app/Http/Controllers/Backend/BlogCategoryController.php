<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('backend.blogs.category.index', compact('categories'));
    }

    public function create()
    {
        return view('backend.blogs.category.create');
    }

    public function store(Request $request)
    {
        $this->validatePayload($request)->validate();

        BlogCategory::create([
            'name'       => $request->name,
            'slug'       => $this->uniqueSlug($request->name),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-blog-category.index')
            ->with('message', 'Blog category added successfully.');
    }

    public function edit($id)
    {
        $category = BlogCategory::whereNull('deleted_by')->findOrFail($id);

        return view('backend.blogs.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request)->validate();

        $slug = $category->slug;
        if ($request->name !== $category->name || empty($slug)) {
            $slug = $this->uniqueSlug($request->name, $category->id);
        }

        $category->update([
            'name'       => $request->name,
            'slug'       => $slug,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-blog-category.index')
            ->with('message', 'Blog category updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $category = BlogCategory::whereNull('deleted_by')->findOrFail($id);
            $category->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-blog-category.index')
                ->with('message', 'Blog category deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Please enter a Category name.',
        ]);
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'category-'.uniqid();
        }

        $slug = $base;
        $i    = 1;
        while (
            BlogCategory::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
