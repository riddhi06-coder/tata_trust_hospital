<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Testimonials;


class MasterTestimonialsController extends Controller
{

    public function index()
    {
        $testimonials = Testimonials::whereNull('deleted_by')->latest()->get();

        return view('backend.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('backend.testimonials.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'      => 'required|string|max:255',
                'testimony' => 'required|string',
            ],
            [
                'name.required'      => 'Please enter Name.',
                'name.max'           => 'Name must be 255 characters or less.',

                'testimony.required' => 'Please enter the Testimony.',
            ]
        );

        $validator->validate();

        Testimonials::create([
            'name'       => $request->name,
            'testimony'  => $request->testimony,
            'is_active'  => $request->boolean('is_active'),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-master-testimonials.index')
            ->with('message', 'Testimonial added successfully.');
    }

    public function edit($id)
    {
        $testimonial = Testimonials::findOrFail($id);
        return view('backend.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonials::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name'      => 'required|string|max:255',
                'testimony' => 'required|string',
            ],
            [
                'name.required'      => 'Please enter Name.',
                'name.max'           => 'Name must be 255 characters or less.',

                'testimony.required' => 'Please enter the Testimony.',
            ]
        );

        $validator->validate();

        $testimonial->update([
            'name'       => $request->name,
            'testimony'  => $request->testimony,
            'is_active'  => $request->boolean('is_active'),
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-master-testimonials.index')
            ->with('message', 'Testimonial updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $testimonial = Testimonials::findOrFail($id);
            $testimonial->update($data);

            return redirect()->route('manage-master-testimonials.index')->with('message', 'Details deleted successfully!');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
