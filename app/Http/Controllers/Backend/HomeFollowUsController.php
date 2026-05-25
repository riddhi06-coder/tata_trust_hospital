<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeFollowUs;


class HomeFollowUsController extends Controller
{

    public function index()
    {
        $followUs = HomeFollowUs::whereNull('deleted_by')->latest()->get();

        return view('backend.home.follow_us.index', compact('followUs'));
    }

    public function create()
    {
        return view('backend.home.follow_us.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'             => 'required|string|max:255',
                'description'       => 'required|string',
                'social_media_link' => 'required|url|max:1000',
                'image'             => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required'       => 'Please enter Heading.',
                'title.max'            => 'Heading must be 255 characters or less.',

                'description.required' => 'Please enter Description.',

                'social_media_link.required' => 'Please enter Social Media Link.',
                'social_media_link.url'      => 'Social Media Link must be a valid URL (e.g. https://www.facebook.com/yourpage).',
                'social_media_link.max'      => 'Social Media Link must be 1000 characters or less.',

                'image.required' => 'Please upload an image.',
                'image.image'    => 'File must be an image.',
                'image.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'      => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder = public_path('home/follow_us');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        HomeFollowUs::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'created_by'        => Auth::id(),
            'created_at'        => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-follow-us.index')
            ->with('message', 'Follow Us entry added successfully.');
    }

    public function edit($id)
    {
        $follow = HomeFollowUs::findOrFail($id);
        return view('backend.home.follow_us.edit', compact('follow'));
    }

    public function update(Request $request, $id)
    {
        $follow = HomeFollowUs::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'title'             => 'required|string|max:255',
                'description'       => 'required|string',
                'social_media_link' => 'required|url|max:1000',
                'image'             => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required'       => 'Please enter Heading.',
                'title.max'            => 'Heading must be 255 characters or less.',

                'description.required' => 'Please enter Description.',

                'social_media_link.required' => 'Please enter Social Media Link.',
                'social_media_link.url'      => 'Social Media Link must be a valid URL.',
                'social_media_link.max'      => 'Social Media Link must be 1000 characters or less.',

                'image.image' => 'File must be an image.',
                'image.mimes' => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'   => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder   = public_path('home/follow_us');
        $fileName = $follow->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            if (!empty($follow->image) && file_exists($folder.'/'.$follow->image)) {
                @unlink($folder.'/'.$follow->image);
            }

            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        $follow->update([
            'title'             => $request->title,
            'description'       => $request->description,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'updated_by'        => Auth::id(),
            'updated_at'        => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-follow-us.index')
            ->with('message', 'Follow Us entry updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $follow = HomeFollowUs::findOrFail($id);
            $follow->update($data);

            return redirect()->route('manage-follow-us.index')->with('message', 'Details deleted successfully!');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
