<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Gallery;
use App\Models\GalleryImage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('backend.gallery.index', compact('images'));
    }

    public function create()
    {
        $hasImages    = GalleryImage::whereNull('deleted_by')->exists();
        $showBanner   = ! $hasImages;
        $gallery      = $showBanner ? Gallery::whereNull('deleted_by')->first() : null;

        return view('backend.gallery.create', compact('showBanner', 'gallery'));
    }

    public function store(Request $request)
    {
        $hasImages  = GalleryImage::whereNull('deleted_by')->exists();
        $showBanner = ! $hasImages;

        $rules = [
            'images'   => 'required|array|min:1',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        if ($showBanner) {
            $rules['banner_heading'] = 'nullable|string|max:255';
            $rules['banner_media']   = 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240';
        }

        $validator = Validator::make($request->all(), $rules, [
            'images.required' => 'Please select at least one image.',
            'images.min'      => 'Please select at least one image.',
            'images.*.image'  => 'Each file must be an image.',
            'images.*.mimes'  => 'Allowed image formats: jpg, jpeg, png, webp.',
            'images.*.max'    => 'Each image must be 5MB or smaller.',
            'banner_media.mimes' => 'Banner: allowed formats jpg, jpeg, png, webp, mp4, webm.',
            'banner_media.max'   => 'Banner must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder = public_path('home/gallery');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveBannerSettings($request, $folder);
        }

        $count = 0;
        foreach ($request->file('images', []) as $file) {
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);

            GalleryImage::create([
                'image'      => $fileName,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            $count++;
        }

        return redirect()
            ->route('manage-gallery.index')
            ->with('message', $count.' image(s) added successfully.');
    }

    public function edit($id)
    {
        $image      = GalleryImage::findOrFail($id);
        $firstId    = GalleryImage::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $image->id === (int) $firstId);
        $gallery    = $showBanner ? Gallery::whereNull('deleted_by')->first() : null;

        return view('backend.gallery.edit', compact('image', 'showBanner', 'gallery'));
    }

    public function update(Request $request, $id)
    {
        $image      = GalleryImage::findOrFail($id);
        $firstId    = GalleryImage::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $image->id === (int) $firstId);

        $rules = [
            'image'        => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'show_on_home' => 'nullable|boolean',
        ];

        if ($showBanner) {
            $rules['banner_heading'] = 'nullable|string|max:255';
            $rules['banner_media']   = 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240';
        }

        $validator = Validator::make($request->all(), $rules, [
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'   => 'Image must be 5MB or smaller.',
            'banner_media.mimes' => 'Banner: allowed formats jpg, jpeg, png, webp, mp4, webm.',
            'banner_media.max'   => 'Banner must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder   = public_path('home/gallery');
        $fileName = $image->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            if (!empty($image->image) && file_exists($folder.'/'.$image->image)) {
                @unlink($folder.'/'.$image->image);
            }

            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        if ($showBanner) {
            $this->saveBannerSettings($request, $folder);
        }

        $image->update([
            'image'        => $fileName,
            'show_on_home' => (bool) $request->boolean('show_on_home'),
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-gallery.index')
            ->with('message', 'Image updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $image = GalleryImage::findOrFail($id);
            $image->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-gallery.index')
                ->with('message', 'Image deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    public function toggleHome(Request $request, string $id)
    {
        try {
            $image = GalleryImage::findOrFail($id);
            $image->show_on_home = ! $image->show_on_home;
            $image->updated_by   = Auth::id();
            $image->save();

            return response()->json([
                'success'      => true,
                'show_on_home' => $image->show_on_home,
                'message'      => $image->show_on_home
                    ? 'Image is now shown on Home.'
                    : 'Image is no longer shown on Home.',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong - '.$ex->getMessage(),
            ], 500);
        }
    }

    private function saveBannerSettings(Request $request, string $folder): void
    {
        $gallery = Gallery::whereNull('deleted_by')->first();

        $fileName  = $gallery->banner_media ?? null;
        $mediaType = $gallery->media_type ?? null;

        if ($request->hasFile('banner_media')) {
            if (!empty($fileName) && file_exists($folder.'/'.$fileName)) {
                @unlink($folder.'/'.$fileName);
            }

            $file     = $request->file('banner_media');
            $ext      = strtolower($file->getClientOriginalExtension());
            $fileName = time().'_'.uniqid().'.'.$ext;
            $file->move($folder, $fileName);

            $mediaType = in_array($ext, ['mp4', 'webm']) ? 'video' : 'image';
        }

        $data = [
            'banner_heading' => $request->banner_heading,
            'banner_media'   => $fileName,
            'media_type'     => $mediaType,
        ];

        if ($gallery) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $gallery->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            Gallery::create($data);
        }
    }
}
