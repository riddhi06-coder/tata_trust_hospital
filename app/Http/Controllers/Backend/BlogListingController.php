<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogListing;
use App\Models\BlogListingSetting;
use App\Models\BlogListingTag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogListingController extends Controller
{
    private const BANNER_DIR    = 'home/blog/banner';
    private const THUMBNAIL_DIR = 'home/blog/thumbnails';

    public function index()
    {
        $listings = BlogListing::whereNull('deleted_by')
            ->with('category')
            ->orderByDesc('id')
            ->get();

        return view('backend.blogs.listing.index', compact('listings'));
    }

    public function create()
    {
        $hasListings = BlogListing::whereNull('deleted_by')->exists();
        $showBanner  = ! $hasListings;
        $settings    = $showBanner ? BlogListingSetting::whereNull('deleted_by')->first() : null;
        $categories  = BlogCategory::whereNull('deleted_by')->orderBy('name')->get();

        return view('backend.blogs.listing.create', compact('showBanner', 'settings', 'categories'));
    }

    public function store(Request $request)
    {
        $hasListings = BlogListing::whereNull('deleted_by')->exists();
        $showBanner  = ! $hasListings;

        $this->validatePayload($request, false, $showBanner)->validate();

        $bannerDir    = public_path(self::BANNER_DIR);
        $thumbnailDir = public_path(self::THUMBNAIL_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($thumbnailDir);

        DB::transaction(function () use ($request, $showBanner, $bannerDir, $thumbnailDir) {
            if ($showBanner) {
                $this->saveBannerSettings($request, $bannerDir);
            }

            $thumbnail = $this->uploadFile($request, 'thumbnail', $thumbnailDir, 'blog');

            $listing = BlogListing::create([
                'blog_category_id'  => $request->blog_category_id,
                'title'             => $request->title,
                'slug'              => $this->uniqueSlug($request->title),
                'thumbnail'         => $thumbnail,
                'short_description' => $request->short_description,
                'blog_date'         => $request->blog_date ?: Carbon::now(),
                'created_by'        => Auth::id(),
                'created_at'        => Carbon::now(),
            ]);

            $this->createTags($listing, $request);
        });

        return redirect()
            ->route('manage-blogs-listing.index')
            ->with('message', 'Blog added successfully.');
    }

    public function edit($id)
    {
        $listing    = BlogListing::whereNull('deleted_by')->findOrFail($id);
        $firstId    = BlogListing::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $listing->id === (int) $firstId);
        $settings   = $showBanner ? BlogListingSetting::whereNull('deleted_by')->first() : null;
        $categories = BlogCategory::whereNull('deleted_by')->orderBy('name')->get();
        $tags       = $listing->tags()->whereNull('deleted_by')->get();

        return view('backend.blogs.listing.edit', compact('listing', 'showBanner', 'settings', 'categories', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $listing    = BlogListing::whereNull('deleted_by')->findOrFail($id);
        $firstId    = BlogListing::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $listing->id === (int) $firstId);

        $this->validatePayload($request, true, $showBanner)->validate();

        $bannerDir    = public_path(self::BANNER_DIR);
        $thumbnailDir = public_path(self::THUMBNAIL_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($thumbnailDir);

        DB::transaction(function () use ($request, $listing, $showBanner, $bannerDir, $thumbnailDir) {
            if ($showBanner) {
                $this->saveBannerSettings($request, $bannerDir);
            }

            $thumbnail = $listing->thumbnail;
            if ($request->hasFile('thumbnail')) {
                $this->deleteFile($thumbnailDir, $thumbnail);
                $thumbnail = $this->uploadFile($request, 'thumbnail', $thumbnailDir, 'blog');
            }

            $slug = $listing->slug;
            if ($request->title !== $listing->title || empty($slug)) {
                $slug = $this->uniqueSlug($request->title, $listing->id);
            }

            $listing->update([
                'blog_category_id'  => $request->blog_category_id,
                'title'             => $request->title,
                'slug'              => $slug,
                'thumbnail'         => $thumbnail,
                'short_description' => $request->short_description,
                'blog_date'         => $request->blog_date ?: $listing->blog_date,
                'updated_by'        => Auth::id(),
                'updated_at'        => Carbon::now(),
            ]);

            $this->syncTags($listing, $request);
        });

        return redirect()
            ->route('manage-blogs-listing.index')
            ->with('message', 'Blog updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $listing = BlogListing::whereNull('deleted_by')->findOrFail($id);

            DB::transaction(function () use ($listing) {
                $now    = Carbon::now();
                $userId = Auth::id();

                $listing->tags()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $listing->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
            });

            return redirect()
                ->route('manage-blogs-listing.index')
                ->with('message', 'Blog deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request, bool $isUpdate, bool $showBanner)
    {
        $thumbRule = ($isUpdate ? 'nullable' : 'required')
            .'|file|image|mimes:jpg,jpeg,png,webp|max:5120';

        $rules = [
            'blog_category_id'  => 'required|exists:blog_categories,id',
            'title'             => 'required|string|max:500',
            'thumbnail'         => $thumbRule,
            'short_description' => 'required|string',
            'blog_date'         => 'nullable|date',

            'tags'              => 'nullable|array',
            'tags.*.tag'        => 'required_with:tags.*|string|max:100',
        ];

        if ($showBanner) {
            $rules['banner_heading'] = 'nullable|string|max:500';
            $rules['banner_image']   = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
        }

        return Validator::make($request->all(), $rules, [
            'blog_category_id.required' => 'Please select a Category.',
            'title.required'            => 'Please enter a Blog Title.',
            'short_description.required'=> 'Please enter a Short Description.',
            'thumbnail.required'        => 'Please upload a Thumbnail image.',
            'tags.*.tag.required_with'  => 'Each tag row needs a value.',
        ]);
    }

    private function ensureFolder(string $folder): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    private function uploadFile(Request $request, string $key, string $folder, string $suffix): ?string
    {
        if (!$request->hasFile($key)) {
            return null;
        }
        $file = $request->file($key);
        $name = time().'_'.uniqid().'_'.$suffix.'.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);
        return $name;
    }

    private function deleteFile(string $folder, ?string $name): void
    {
        if (!empty($name) && file_exists($folder.'/'.$name)) {
            @unlink($folder.'/'.$name);
        }
    }

    private function saveBannerSettings(Request $request, string $folder): void
    {
        $settings = BlogListingSetting::whereNull('deleted_by')->first();

        $bannerImageName = $settings->banner_image ?? null;

        if ($request->hasFile('banner_image')) {
            if (!empty($bannerImageName) && file_exists($folder.'/'.$bannerImageName)) {
                @unlink($folder.'/'.$bannerImageName);
            }
            $file            = $request->file('banner_image');
            $bannerImageName = time().'_'.uniqid().'_banner.'.$file->getClientOriginalExtension();
            $file->move($folder, $bannerImageName);
        }

        $data = [
            'banner_heading' => $request->banner_heading,
            'banner_image'   => $bannerImageName,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            BlogListingSetting::create($data);
        }
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'blog-'.uniqid();
        }

        $slug = $base;
        $i    = 1;
        while (
            BlogListing::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function createTags(BlogListing $listing, Request $request): void
    {
        $rows = $request->input('tags', []);
        $sort = 0;

        foreach ($rows as $row) {
            $tag = trim((string) ($row['tag'] ?? ''));
            if ($tag === '') { continue; }

            BlogListingTag::create([
                'blog_listing_id' => $listing->id,
                'tag'             => $tag,
                'sort_order'      => $sort++,
                'created_by'      => Auth::id(),
                'created_at'      => Carbon::now(),
            ]);
        }
    }

    private function syncTags(BlogListing $listing, Request $request): void
    {
        $rows    = $request->input('tags', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $row) {
            $tag = trim((string) ($row['tag'] ?? ''));
            if ($tag === '') { continue; }

            $rowId = isset($row['id']) ? (int) $row['id'] : 0;

            if ($rowId > 0) {
                $existing = BlogListingTag::where('blog_listing_id', $listing->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);
                if (!$existing) { continue; }

                $existing->update([
                    'tag'        => $tag,
                    'sort_order' => $sort++,
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $created = BlogListingTag::create([
                    'blog_listing_id' => $listing->id,
                    'tag'             => $tag,
                    'sort_order'      => $sort++,
                    'created_by'      => $userId,
                    'created_at'      => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        BlogListingTag::where('blog_listing_id', $listing->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
    }
}
