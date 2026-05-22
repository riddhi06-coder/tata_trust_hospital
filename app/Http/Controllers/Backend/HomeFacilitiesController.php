<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeFacilities;


class HomeFacilitiesController extends Controller
{

    public function index()
    {
        $facilities = HomeFacilities::latest()->get();

        return view('backend.home.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('backend.home.facilities.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',

                'names'       => 'required|array|min:1',
                'names.*'     => 'required|string|max:255',
                'icons'       => 'required|array|min:1',
                'icons.*'     => 'required|file|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            ],
            [
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',

                'names.required'   => 'Please add at least one facility.',
                'names.*.required' => 'Facility name is required.',
                'names.*.max'      => 'Facility name must be 255 characters or less.',

                'icons.required'   => 'Please upload at least one image.',
                'icons.*.required' => 'Each facility must have an image.',
                'icons.*.image'    => 'Image must be an image file.',
                'icons.*.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp, svg.',
                'icons.*.max'      => 'Each image must be 2MB or smaller.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation — every row needs both an image and a name
        |--------------------------------------------------------------------------
        */
        $validator->after(function ($validator) use ($request) {
            $names = $request->input('names', []);
            $icons = $request->file('icons', []);

            foreach ($names as $idx => $name) {
                if (empty($icons[$idx])) {
                    $validator->errors()->add("icons.$idx", 'Row '.((int) $idx + 1).': image is required.');
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Upload images & build the rows
        |--------------------------------------------------------------------------
        */
        $folder = public_path('home/facilities');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $rows  = [];
        $names = $request->input('names', []);
        $icons = $request->file('icons', []);

        foreach ($names as $idx => $name) {
            $file     = $icons[$idx];
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);

            $rows[] = [
                'icon' => $fileName,
                'name' => $name,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Store (facilities is cast as array → stored as JSON)
        |--------------------------------------------------------------------------
        */
        HomeFacilities::create([
            'title'       => $request->title,
            'description' => $request->description,
            'facilities'  => $rows,
            'created_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-facilities.index')
            ->with('message', 'Facilities added successfully.');
    }

    public function edit($id)
    {
        $facility = HomeFacilities::findOrFail($id);
        return view('backend.home.facilities.edit', compact('facility'));
    }

    public function update(Request $request, $id)
    {
        $facility = HomeFacilities::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'title'            => 'required|string|max:255',
                'description'      => 'required|string',

                'names'            => 'required|array|min:1',
                'names.*'          => 'required|string|max:255',

                'icons'            => 'array',
                'icons.*'          => 'nullable|file|image|mimes:jpg,jpeg,png,webp,svg|max:2048',

                'existing_icons'   => 'array',
                'existing_icons.*' => 'nullable|string',
            ],
            [
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',

                'names.required'   => 'Please add at least one facility.',
                'names.*.required' => 'Facility name is required.',
                'names.*.max'      => 'Facility name must be 255 characters or less.',

                'icons.*.image' => 'Image must be an image file.',
                'icons.*.mimes' => 'Allowed image formats: jpg, jpeg, png, webp, svg.',
                'icons.*.max'   => 'Each image must be 2MB or smaller.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation — every row needs either a new image or an existing one
        |--------------------------------------------------------------------------
        */
        $validator->after(function ($validator) use ($request) {
            $names    = $request->input('names', []);
            $icons    = $request->file('icons', []);
            $existing = $request->input('existing_icons', []);

            foreach ($names as $idx => $name) {
                $hasNew      = !empty($icons[$idx]);
                $hasExisting = !empty($existing[$idx]);

                if (!$hasNew && !$hasExisting) {
                    $validator->errors()->add("icons.$idx", 'Row '.((int) $idx + 1).': image is required.');
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Replace icons on disk where a new file was uploaded; clean up orphans
        |--------------------------------------------------------------------------
        */
        $folder = public_path('home/facilities');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $oldRows  = $facility->facilities ?? [];
        $oldIcons = array_filter(array_column($oldRows, 'icon'));

        $rows     = [];
        $names    = $request->input('names', []);
        $icons    = $request->file('icons', []);
        $existing = $request->input('existing_icons', []);

        foreach ($names as $idx => $name) {

            $newFile = $icons[$idx] ?? null;
            $oldName = $existing[$idx] ?? null;

            if ($newFile) {
                $fileName = time().'_'.uniqid().'.'.$newFile->getClientOriginalExtension();
                $newFile->move($folder, $fileName);

                if ($oldName && file_exists($folder.'/'.$oldName)) {
                    @unlink($folder.'/'.$oldName);
                }

                $finalIcon = $fileName;
            } else {
                $finalIcon = $oldName;
            }

            $rows[] = [
                'icon' => $finalIcon,
                'name' => $name,
            ];
        }

        // Delete icons no longer referenced
        $stillUsed   = array_column($rows, 'icon');
        $orphanIcons = array_diff($oldIcons, $stillUsed);
        foreach ($orphanIcons as $orphan) {
            if ($orphan && file_exists($folder.'/'.$orphan)) {
                @unlink($folder.'/'.$orphan);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        $facility->update([
            'title'       => $request->title,
            'description' => $request->description,
            'facilities'  => $rows,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-facilities.index')
            ->with('message', 'Facilities updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = HomeFacilities::findOrFail($id);
            $industries->update($data);

            return redirect()->route('home-specialities.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
