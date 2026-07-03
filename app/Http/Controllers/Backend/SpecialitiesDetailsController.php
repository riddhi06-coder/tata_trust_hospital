<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Specialities;
use App\Models\SpecialitiesDetails;

class SpecialitiesDetailsController extends Controller
{
    public function index()
    {
        $details = SpecialitiesDetails::whereNull('deleted_by')
            ->with('speciality')
            ->orderBy('id')
            ->get();

        return view('backend.specialities.details.index', compact('details'));
    }

    public function create()
    {
        $specialities = Specialities::whereNull('deleted_by')->orderBy('speciality')->get();

        return view('backend.specialities.details.create', compact('specialities'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'speciality_id'       => 'required|exists:specialities,id',
                'banner_image'        => 'required|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'section_image'       => 'required|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'section_heading'     => 'required|string|max:255',
                'section_description' => 'required|string',
                'service_heading'     => 'required|string|max:255',
                'services'            => 'required|array|min:1',
                'services.*'          => 'required|string|max:2000',
                'short_info'          => 'nullable|string|max:2000',
            ],
            [
                'speciality_id.required'       => 'Please select a Speciality.',
                'speciality_id.exists'         => 'Selected Speciality is invalid.',
                'banner_image.required'        => 'Please upload a Banner Image.',
                'banner_image.image'           => 'Banner Image must be an image.',
                'banner_image.mimes'           => 'Allowed formats: jpg, jpeg, png, webp.',
                'banner_image.max'             => 'Banner Image must be 10MB or smaller.',
                'section_image.required'       => 'Please upload a Section Image.',
                'section_image.image'          => 'Section Image must be an image.',
                'section_image.mimes'          => 'Allowed formats: jpg, jpeg, png, webp.',
                'section_image.max'            => 'Section Image must be 10MB or smaller.',
                'section_heading.required'     => 'Please enter Section Heading.',
                'section_description.required' => 'Please enter Section Description.',
                'service_heading.required'     => 'Please enter Service Heading.',
                'services.required'            => 'Please add at least one service description.',
                'services.min'                 => 'Please add at least one service description.',
                'services.*.required'          => 'Each service description is required.',
                'services.*.max'               => 'Each service description must be 2000 characters or less.',
            ]
        );

        $validator->validate();

        $folder = public_path('home/speciality-details');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $bannerFile = $request->file('banner_image');
        $bannerName = time().'_'.uniqid().'_banner.'.$bannerFile->getClientOriginalExtension();
        $bannerFile->move($folder, $bannerName);

        $sectionFile = $request->file('section_image');
        $sectionName = time().'_'.uniqid().'_section.'.$sectionFile->getClientOriginalExtension();
        $sectionFile->move($folder, $sectionName);

        $services = array_values(array_filter(
            (array) $request->input('services', []),
            fn ($s) => is_string($s) && trim($s) !== ''
        ));

        SpecialitiesDetails::create([
            'speciality_id'       => $request->speciality_id,
            'banner_image'        => $bannerName,
            'section_image'       => $sectionName,
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
            'service_heading'     => $request->service_heading,
            'services'            => $services,
            'short_info'          => $request->short_info,
            'created_by'          => Auth::id(),
            'created_at'          => Carbon::now(),
        ]);

        return redirect()
            ->route('speciality-details.index')
            ->with('message', 'Speciality Details added successfully.');
    }

    public function edit($id)
    {
        $detail       = SpecialitiesDetails::findOrFail($id);
        $specialities = Specialities::whereNull('deleted_by')->orderBy('speciality')->get();

        return view('backend.specialities.details.edit', compact('detail', 'specialities'));
    }

    public function update(Request $request, $id)
    {
        $detail = SpecialitiesDetails::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'speciality_id'       => 'required|exists:specialities,id',
                'banner_image'        => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'section_image'       => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'section_heading'     => 'required|string|max:255',
                'section_description' => 'required|string',
                'service_heading'     => 'required|string|max:255',
                'services'            => 'required|array|min:1',
                'services.*'          => 'required|string|max:2000',
                'short_info'          => 'nullable|string|max:2000',
            ],
            [
                'speciality_id.required'       => 'Please select a Speciality.',
                'speciality_id.exists'         => 'Selected Speciality is invalid.',
                'banner_image.image'           => 'Banner Image must be an image.',
                'banner_image.mimes'           => 'Allowed formats: jpg, jpeg, png, webp.',
                'banner_image.max'             => 'Banner Image must be 10MB or smaller.',
                'section_image.image'          => 'Section Image must be an image.',
                'section_image.mimes'          => 'Allowed formats: jpg, jpeg, png, webp.',
                'section_image.max'            => 'Section Image must be 10MB or smaller.',
                'section_heading.required'     => 'Please enter Section Heading.',
                'section_description.required' => 'Please enter Section Description.',
                'service_heading.required'     => 'Please enter Service Heading.',
                'services.required'            => 'Please add at least one service description.',
                'services.min'                 => 'Please add at least one service description.',
                'services.*.required'          => 'Each service description is required.',
                'services.*.max'               => 'Each service description must be 2000 characters or less.',
            ]
        );

        $validator->validate();

        $folder      = public_path('home/speciality-details');
        $bannerName  = $detail->banner_image;
        $sectionName = $detail->section_image;

        if ($request->hasFile('banner_image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (!empty($detail->banner_image) && file_exists($folder.'/'.$detail->banner_image)) {
                @unlink($folder.'/'.$detail->banner_image);
            }
            $file       = $request->file('banner_image');
            $bannerName = time().'_'.uniqid().'_banner.'.$file->getClientOriginalExtension();
            $file->move($folder, $bannerName);
        }

        if ($request->hasFile('section_image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (!empty($detail->section_image) && file_exists($folder.'/'.$detail->section_image)) {
                @unlink($folder.'/'.$detail->section_image);
            }
            $file        = $request->file('section_image');
            $sectionName = time().'_'.uniqid().'_section.'.$file->getClientOriginalExtension();
            $file->move($folder, $sectionName);
        }

        $services = array_values(array_filter(
            (array) $request->input('services', []),
            fn ($s) => is_string($s) && trim($s) !== ''
        ));

        $detail->update([
            'speciality_id'       => $request->speciality_id,
            'banner_image'        => $bannerName,
            'section_image'       => $sectionName,
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
            'service_heading'     => $request->service_heading,
            'services'            => $services,
            'short_info'          => $request->short_info,
            'updated_by'          => Auth::id(),
            'updated_at'          => Carbon::now(),
        ]);

        return redirect()
            ->route('speciality-details.index')
            ->with('message', 'Speciality Details updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $detail = SpecialitiesDetails::findOrFail($id);
            $detail->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('speciality-details.index')
                ->with('message', 'Speciality Details deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }
}
