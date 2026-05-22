<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeBanner;



class HomeBannerController extends Controller
{
 
    public function index()
    {
        $banners = HomeBanner::wherenull('deleted_by')->get();
        return view('backend.home.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('backend.home.banners.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'banner_heading' => 'required|string|max:255',
                'banner_title'   => 'nullable|string|max:255',

                'banner_media'   => 'required|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:5120',
            ],

            [
                'banner_media.required' => 'Please upload banner image or video.',
                'banner_media.file'     => 'Uploaded file is invalid.',
                'banner_media.mimes'    => 'Only jpg, jpeg, png, webp, mp4 and webm files are allowed.',
                'banner_media.max'      => 'File size should not exceed 5MB.',

                'banner_heading.max'    => 'Banner heading should not exceed 255 characters.',
                'banner_title.max'      => 'Banner title should not exceed 255 characters.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation
        |--------------------------------------------------------------------------
        | If uploaded file is IMAGE then banner heading is required
        |
        */

        $validator->after(function ($validator) use ($request) {

            if ($request->hasFile('banner_media')) {

                $mime = $request->file('banner_media')->getMimeType();

                // If image uploaded
                if (
                    str_starts_with($mime, 'image/')
                    && !$request->filled('banner_heading')
                ) {

                    $validator->errors()->add(
                        'banner_heading',
                        'Banner heading is required when uploading an image.'
                    );
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Upload Media
        |--------------------------------------------------------------------------
        */

        $fileName  = null;
        $mediaType = null;

        if ($request->hasFile('banner_media')) {

            $file = $request->file('banner_media');

            // Detect media type
            $mediaType = str_starts_with(
                $file->getMimeType(),
                'video'
            ) ? 'video' : 'image';

            // Folder path
            $folder = public_path('home/bannerimagevideo');

            // Create folder if not exists
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move file
            $file->move($folder, $fileName);
        }

        /*
        |--------------------------------------------------------------------------
        | Store Data
        |--------------------------------------------------------------------------
        */

        HomeBanner::create([

            'banner_heading' => $request->banner_heading,
            'banner_title'   => $request->banner_title,

            'banner_media'   => $fileName,
            'media_type'     => $mediaType,

            'created_by'     => Auth::id(),
            'created_at'     => Carbon::now(),
        ]);

        return redirect()->route('banner-details.index')->with('message', 'Banner added successfully.');
    }

    public function edit($id)
    {
        $slider = HomeBanner::findOrFail($id);
        return view('backend.home.banners.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $banner = HomeBanner::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'banner_heading' => 'required|string|max:255',
                'banner_title'   => 'nullable|string|max:255',

                'banner_media'   => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:5120',
            ],

            [
                'banner_heading.required' => 'Please enter banner heading.',

                'banner_media.file'       => 'Uploaded file is invalid.',
                'banner_media.mimes'      => 'Only jpg, jpeg, png, webp, mp4 and webm files are allowed.',
                'banner_media.max'        => 'File size should not exceed 5MB.',

                'banner_heading.max'      => 'Banner heading should not exceed 255 characters.',
                'banner_title.max'        => 'Banner title should not exceed 255 characters.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation
        |--------------------------------------------------------------------------
        */

        $validator->after(function ($validator) use ($request) {

            if ($request->hasFile('banner_media')) {

                $mime = $request->file('banner_media')->getMimeType();

                // If image uploaded & heading empty
                if (
                    str_starts_with($mime, 'image/')
                    && !$request->filled('banner_heading')
                ) {

                    $validator->errors()->add(
                        'banner_heading',
                        'Banner heading is required when uploading an image.'
                    );
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Upload Media
        |--------------------------------------------------------------------------
        */

        $fileName  = $banner->banner_media;
        $mediaType = $banner->media_type;

        if ($request->hasFile('banner_media')) {

            $file = $request->file('banner_media');

            // Detect media type
            $mediaType = str_starts_with(
                $file->getMimeType(),
                'video'
            ) ? 'video' : 'image';

            // Folder path
            $folder = public_path('home/bannerimagevideo');

            // Create folder if not exists
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Delete old file
            if (
                !empty($banner->banner_media)
                && file_exists($folder . '/' . $banner->banner_media)
            ) {
                unlink($folder . '/' . $banner->banner_media);
            }

            // Generate unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move uploaded file
            $file->move($folder, $fileName);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Data
        |--------------------------------------------------------------------------
        */

        $banner->update([

            'banner_heading' => $request->banner_heading,
            'banner_title'   => $request->banner_title,

            'banner_media'   => $fileName,
            'media_type'     => $mediaType,

            'updated_by'     => Auth::id(),
            'updated_at'     => Carbon::now(),
        ]);

        return redirect()
            ->route('banner-details.index')
            ->with('message', 'Banner updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = HomeBanner::findOrFail($id);
            $industries->update($data);

            return redirect()->route('banner-details.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
    
}