<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogDetails;
use App\Models\BlogDetailSocialLink;
use App\Models\BlogListing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlogDetailsController extends Controller
{
    private const IMAGE_DIR = 'home/blog/details';

    public function index()
    {
        $details = BlogDetails::whereNull('deleted_by')
            ->with('listing')
            ->orderByDesc('id')
            ->get();

        return view('backend.blogs.details.index', compact('details'));
    }

    public function create()
    {
        // Only offer listings that don't have a detail record yet.
        $listings = BlogListing::whereNull('deleted_by')
            ->whereDoesntHave('detail')
            ->orderBy('title')
            ->get();

        return view('backend.blogs.details.create', [
            'listings'  => $listings,
            'platforms' => BlogDetailSocialLink::PLATFORMS,
        ]);
    }

    public function store(Request $request)
    {
        $this->validatePayload($request, false)->validate();

        // Guard: a listing can only have one detail record.
        if (BlogDetails::where('blog_listing_id', $request->blog_listing_id)->whereNull('deleted_by')->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This blog already has a detail page. Edit the existing one instead.');
        }

        $folder = public_path(self::IMAGE_DIR);
        $this->ensureFolder($folder);

        DB::transaction(function () use ($request, $folder) {
            $image = $this->uploadFile($request, 'image', $folder, 'detail');

            $detail = BlogDetails::create([
                'blog_listing_id' => $request->blog_listing_id,
                'image'           => $image,
                'information'     => $request->information,
                'created_by'      => Auth::id(),
                'created_at'      => Carbon::now(),
            ]);

            $this->createSocialLinks($detail, $request);
        });

        return redirect()
            ->route('manage-blog-details.index')
            ->with('message', 'Blog detail added successfully.');
    }

    public function edit($id)
    {
        $detail   = BlogDetails::whereNull('deleted_by')->findOrFail($id);
        // Include the current listing + any listing that still doesn't have a detail record.
        $listings = BlogListing::whereNull('deleted_by')
            ->where(fn ($q) => $q->whereDoesntHave('detail')->orWhere('id', $detail->blog_listing_id))
            ->orderBy('title')
            ->get();
        $socials  = $detail->socialLinks()->whereNull('deleted_by')->get();

        return view('backend.blogs.details.edit', [
            'detail'    => $detail,
            'listings'  => $listings,
            'socials'   => $socials,
            'platforms' => BlogDetailSocialLink::PLATFORMS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $detail = BlogDetails::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request, true)->validate();

        // Guard: if the admin changes the linked listing, ensure the new one doesn't already have a detail.
        if ((int) $request->blog_listing_id !== (int) $detail->blog_listing_id) {
            $exists = BlogDetails::where('blog_listing_id', $request->blog_listing_id)
                ->whereNull('deleted_by')
                ->where('id', '!=', $detail->id)
                ->exists();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The chosen blog already has a detail page.');
            }
        }

        $folder = public_path(self::IMAGE_DIR);
        $this->ensureFolder($folder);

        DB::transaction(function () use ($request, $detail, $folder) {
            $image = $detail->image;
            if ($request->hasFile('image')) {
                $this->deleteFile($folder, $image);
                $image = $this->uploadFile($request, 'image', $folder, 'detail');
            }

            $detail->update([
                'blog_listing_id' => $request->blog_listing_id,
                'image'           => $image,
                'information'     => $request->information,
                'updated_by'      => Auth::id(),
                'updated_at'      => Carbon::now(),
            ]);

            $this->syncSocialLinks($detail, $request);
        });

        return redirect()
            ->route('manage-blog-details.index')
            ->with('message', 'Blog detail updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $detail = BlogDetails::whereNull('deleted_by')->findOrFail($id);

            DB::transaction(function () use ($detail) {
                $now    = Carbon::now();
                $userId = Auth::id();

                $detail->socialLinks()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $detail->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
            });

            return redirect()
                ->route('manage-blog-details.index')
                ->with('message', 'Blog detail deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request, bool $isUpdate)
    {
        $imageRule = ($isUpdate ? 'nullable' : 'required')
            .'|file|image|mimes:jpg,jpeg,png,webp|max:10240';

        // Social links are optional. Drop fully-blank rows before validating so an
        // untouched empty row doesn't trip required_with (empty inputs submit as ""
        // which counts as "present"). Partially-filled rows are kept and validated.
        $social = collect($request->input('social', []))
            ->filter(fn ($row) => filled($row['platform'] ?? null) || filled($row['url'] ?? null))
            ->values()
            ->all();
        $request->merge(['social' => $social]);

        return Validator::make($request->all(), [
            'blog_listing_id' => 'required|exists:blog_listings,id',
            'image'           => $imageRule,
            'information'     => 'required|string',

            'social'                => 'nullable|array',
            'social.*.platform'     => 'nullable|required_with:social.*.url|string|in:'.implode(',', array_keys(BlogDetailSocialLink::PLATFORMS)),
            'social.*.url'          => 'nullable|required_with:social.*.platform|string|max:2048',
        ], [
            'blog_listing_id.required' => 'Please select a Blog Title.',
            'image.required'           => 'Please upload an Image.',
            'information.required'     => 'Please enter the Information content.',
            'social.*.platform.in'          => 'Choose a valid social platform.',
            'social.*.platform.required_with' => 'Choose a social platform for each row.',
            'social.*.url.required_with'    => 'Each social row needs a URL.',
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

    private function createSocialLinks(BlogDetails $detail, Request $request): void
    {
        $rows = $request->input('social', []);
        $sort = 0;

        foreach ($rows as $row) {
            if (empty($row['platform']) || empty($row['url'])) { continue; }

            BlogDetailSocialLink::create([
                'blog_detail_id' => $detail->id,
                'platform'       => $row['platform'],
                'url'            => $row['url'],
                'sort_order'     => $sort++,
                'created_by'     => Auth::id(),
                'created_at'     => Carbon::now(),
            ]);
        }
    }

    private function syncSocialLinks(BlogDetails $detail, Request $request): void
    {
        $rows    = $request->input('social', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $row) {
            if (empty($row['platform']) || empty($row['url'])) { continue; }

            $rowId = isset($row['id']) ? (int) $row['id'] : 0;
            $data  = [
                'platform'   => $row['platform'],
                'url'        => $row['url'],
                'sort_order' => $sort++,
            ];

            if ($rowId > 0) {
                $existing = BlogDetailSocialLink::where('blog_detail_id', $detail->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);
                if (!$existing) { continue; }

                $existing->update($data + [
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $created = BlogDetailSocialLink::create($data + [
                    'blog_detail_id' => $detail->id,
                    'created_by'     => $userId,
                    'created_at'     => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        BlogDetailSocialLink::where('blog_detail_id', $detail->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
    }
}
