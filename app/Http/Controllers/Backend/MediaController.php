<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\MediaSetting;
use App\Models\Media;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('backend.media.index', compact('media'));
    }

    public function create()
    {
        $hasMedia   = Media::whereNull('deleted_by')->exists();
        $showBanner = ! $hasMedia;
        $settings   = $showBanner ? MediaSetting::whereNull('deleted_by')->first() : null;

        return view('backend.media.create', compact('showBanner', 'settings'));
    }

    public function store(Request $request)
    {
        $hasMedia   = Media::whereNull('deleted_by')->exists();
        $showBanner = ! $hasMedia;

        $rules = [
            'title'        => 'required|string|max:255',
            'image'        => 'required|file|image|mimes:jpg,jpeg,png,webp|max:10240',
            'article_link' => 'required|url|max:2048',
        ];

        if ($showBanner) {
            $rules['heading']         = 'nullable|string|max:255';
            $rules['section_heading'] = 'nullable|string|max:255';
            $rules['banner_image']    = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120';
        }

        $validator = Validator::make($request->all(), $rules, $this->messages());
        $validator->validate();

        $folder = public_path('home/media');
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request);
        }

        $imgFile = $request->file('image');
        $imgName = time().'_'.uniqid().'_media.'.$imgFile->getClientOriginalExtension();
        $imgFile->move($folder, $imgName);

        Media::create([
            'title'        => $request->title,
            'image'        => $imgName,
            'article_link' => $request->article_link,
            'created_by'   => Auth::id(),
            'created_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-media.index')
            ->with('message', 'Media added successfully.');
    }

    public function edit($id)
    {
        $item       = Media::findOrFail($id);
        $firstId    = Media::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);
        $settings   = $showBanner ? MediaSetting::whereNull('deleted_by')->first() : null;

        return view('backend.media.edit', compact('item', 'showBanner', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $item       = Media::findOrFail($id);
        $firstId    = Media::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);

        $rules = [
            'title'        => 'required|string|max:255',
            'image'        => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240',
            'article_link' => 'required|url|max:2048',
        ];

        if ($showBanner) {
            $rules['heading']         = 'nullable|string|max:255';
            $rules['section_heading'] = 'nullable|string|max:255';
            $rules['banner_image']    = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120';
        }

        $validator = Validator::make($request->all(), $rules, $this->messages());
        $validator->validate();

        $folder  = public_path('home/media');
        $imgName = $item->image;

        if ($request->hasFile('image')) {
            if (! file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (! empty($item->image) && file_exists($folder.'/'.$item->image)) {
                @unlink($folder.'/'.$item->image);
            }
            $imgFile = $request->file('image');
            $imgName = time().'_'.uniqid().'_media.'.$imgFile->getClientOriginalExtension();
            $imgFile->move($folder, $imgName);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request);
        }

        $item->update([
            'title'        => $request->title,
            'image'        => $imgName,
            'article_link' => $request->article_link,
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-media.index')
            ->with('message', 'Media updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $item = Media::findOrFail($id);
            $item->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-media.index')
                ->with('message', 'Media deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /** Validation messages shared by store/update. */
    private function messages(): array
    {
        return [
            'title.required'        => 'Please enter a Title.',
            'image.required'        => 'Please upload an Image.',
            'image.image'           => 'The image must be a valid image file.',
            'image.mimes'           => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'             => 'Image must be 10MB or smaller.',
            'article_link.required' => 'Please enter the Article Link.',
            'article_link.url'      => 'Please enter a valid Article Link (URL).',
            'banner_image.image'    => 'The banner must be a valid image file.',
            'banner_image.mimes'    => 'Allowed banner formats: jpg, jpeg, png, webp.',
            'banner_image.max'      => 'Banner must be 5MB or smaller.',
        ];
    }

    /** Persist the section settings (banner image + headings) on the first record. */
    private function saveSectionSettings(Request $request): void
    {
        $settings = MediaSetting::whereNull('deleted_by')->first();

        $data = [
            'heading'         => $request->heading,
            'section_heading' => $request->section_heading,
        ];

        if ($request->hasFile('banner_image')) {
            $folder = public_path('home/media');
            if (! file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if ($settings && ! empty($settings->banner_image) && file_exists($folder.'/'.$settings->banner_image)) {
                @unlink($folder.'/'.$settings->banner_image);
            }
            $bannerFile = $request->file('banner_image');
            $bannerName = time().'_'.uniqid().'_banner.'.$bannerFile->getClientOriginalExtension();
            $bannerFile->move($folder, $bannerName);
            $data['banner_image'] = $bannerName;
        }

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            MediaSetting::create($data);
        }
    }
}
