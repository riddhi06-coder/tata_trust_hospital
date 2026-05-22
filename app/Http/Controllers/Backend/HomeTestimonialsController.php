<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeTestimonials;


class HomeTestimonialsController extends Controller
{

    public function index()
    {
        $testimonials = HomeTestimonials::whereNull('deleted_by')->latest()->get();

        return view('backend.home.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('backend.home.testimonials.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'image' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required' => 'Please enter Heading.',
                'title.max'      => 'Heading must be 255 characters or less.',

                'image.required' => 'Please upload an image.',
                'image.image'    => 'File must be an image.',
                'image.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'      => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder = public_path('home/testimonials');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        HomeTestimonials::create([
            'title'      => $request->title,
            'image'      => $fileName,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-testimonials.index')
            ->with('message', 'Testimonial added successfully.');
    }

    public function edit($id)
    {
        $testimonial = HomeTestimonials::findOrFail($id);
        return view('backend.home.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        $testimonial = HomeTestimonials::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'image' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required' => 'Please enter Heading.',
                'title.max'      => 'Heading must be 255 characters or less.',

                'image.image' => 'File must be an image.',
                'image.mimes' => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'   => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder   = public_path('home/testimonials');
        $fileName = $testimonial->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            if (!empty($testimonial->image) && file_exists($folder.'/'.$testimonial->image)) {
                @unlink($folder.'/'.$testimonial->image);
            }

            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        $testimonial->update([
            'title'      => $request->title,
            'image'      => $fileName,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-testimonials.index')
            ->with('message', 'Testimonial updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = HomeTestimonials::findOrFail($id);
            $industries->update($data);

            return redirect()->route('manage-testimonials.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
