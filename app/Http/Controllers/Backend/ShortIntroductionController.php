<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ShortIntroduction;


class ShortIntroductionController extends Controller
{
 
    public function index()
    {
        $shortIntroductions = ShortIntroduction::wherenull('deleted_by')->get();
        return view('backend.home.short_intro.index',compact('shortIntroductions'));
    }

    public function create()
    {
        return view('backend.home.short_intro.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'banner_heading' => 'required|string|max:255',
                'banner_title'   => 'nullable|string|max:255',

                'introduction'   => 'required|string',

                'banner_media'   => 'required|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:5120',
            ],

            [
                'banner_heading.required' => 'Please enter banner heading.',
                'banner_heading.max'      => 'Banner heading should not exceed 255 characters.',

                'banner_title.max'        => 'Banner title should not exceed 255 characters.',

                'introduction.required'   => 'Please enter introduction.',

                'banner_media.required'   => 'Please upload banner image or video.',
                'banner_media.file'       => 'Uploaded file is invalid.',
                'banner_media.mimes'      => 'Only jpg, jpeg, png, webp, mp4 and webm files are allowed.',
                'banner_media.max'        => 'File size should not exceed 5MB.',
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
            $folder = public_path('home/shortintroduction');

            // Create folder if not exists
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Generate unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move uploaded file
            $file->move($folder, $fileName);
        }

        /*
        |--------------------------------------------------------------------------
        | Store Data
        |--------------------------------------------------------------------------
        */

        ShortIntroduction::create([

            'banner_heading' => $request->banner_heading,
            'banner_title'   => $request->banner_title,

            'introduction'   => $request->introduction,

            'banner_media'   => $fileName,
            'media_type'     => $mediaType,

            'created_by'     => Auth::id(),
            'created_at'     => Carbon::now(),
        ]);

        return redirect()->route('short-introduction.index')->with('message', 'Short introduction added successfully.');
    }

    public function edit($id)
    {
        $slider = ShortIntroduction::findOrFail($id);
        return view('backend.home.short_intro.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = ShortIntroduction::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'banner_heading' => 'required|string|max:255',
                'banner_title'   => 'nullable|string|max:255',

                'introduction'   => 'required|string',

                'banner_media'   => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:5120',
            ],

            [
                'banner_heading.required' => 'Please enter banner heading.',
                'banner_heading.max'      => 'Banner heading should not exceed 255 characters.',

                'banner_title.max'        => 'Banner title should not exceed 255 characters.',

                'introduction.required'   => 'Please enter introduction.',

                'banner_media.file'       => 'Uploaded file is invalid.',
                'banner_media.mimes'      => 'Only jpg, jpeg, png, webp, mp4 and webm files are allowed.',
                'banner_media.max'        => 'File size should not exceed 5MB.',
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

        $fileName  = $slider->banner_media;
        $mediaType = $slider->media_type;

        if ($request->hasFile('banner_media')) {

            $file = $request->file('banner_media');

            // Detect media type
            $mediaType = str_starts_with(
                $file->getMimeType(),
                'video'
            ) ? 'video' : 'image';

            // Folder path
            $folder = public_path('home/shortintroduction');

            // Create folder if not exists
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Delete old file
            if (
                !empty($slider->banner_media)
                && file_exists($folder . '/' . $slider->banner_media)
            ) {
                unlink($folder . '/' . $slider->banner_media);
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

        $slider->update([

            'banner_heading' => $request->banner_heading,
            'banner_title'   => $request->banner_title,

            'introduction'   => $request->introduction,

            'banner_media'   => $fileName,
            'media_type'     => $mediaType,

            'updated_by'     => Auth::id(),
            'updated_at'     => Carbon::now(),
        ]);

        return redirect()
            ->route('short-introduction.index')
            ->with('message', 'Short introduction updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = ShortIntroduction::findOrFail($id);
            $industries->update($data);

            return redirect()->route('short-introduction.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }

}