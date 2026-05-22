<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeSpecialities;


class HomeSpecialitiesController extends Controller
{

    public function index()
    {
        $specialities = HomeSpecialities::wherenull('deleted_by')->get();

        return view('backend.home.specialities.index',compact('specialities'));
    }

    public function create()
    {
        return view('backend.home.specialities.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'our_motto'   => 'required|string|max:255',
                'title'       => 'required|string|max:255',
                'description' => 'required|string',

                'names'       => 'required|array|min:1',
                'names.*'     => 'required|string|max:255',
                'icons'       => 'required|array|min:1',
                'icons.*'     => 'required|file|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            ],
            [
                'our_motto.required'   => 'Please enter Our Motto.',
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',

                'names.required'   => 'Please add at least one speciality.',
                'names.*.required' => 'Speciality name is required.',
                'names.*.max'      => 'Speciality name must be 255 characters or less.',

                'icons.required'    => 'Please upload at least one icon.',
                'icons.*.required'  => 'Each speciality must have an icon.',
                'icons.*.image'     => 'Icon must be an image.',
                'icons.*.mimes'     => 'Allowed icon formats: jpg, jpeg, png, webp, svg.',
                'icons.*.max'       => 'Each icon must be 2MB or smaller.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation — every row must have both an icon and a name
        |--------------------------------------------------------------------------
        */
        $validator->after(function ($validator) use ($request) {
            $names = $request->input('names', []);
            $icons = $request->file('icons', []);

            foreach ($names as $idx => $name) {
                if (empty($icons[$idx])) {
                    $validator->errors()->add("icons.$idx", 'Row '.((int) $idx + 1).': icon is required.');
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Upload icons & build the rows
        |--------------------------------------------------------------------------
        */
        $folder = public_path('home/specialities');
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
        | Store
        |--------------------------------------------------------------------------
        */
        HomeSpecialities::create([
            'our_motto'    => $request->our_motto,
            'title'        => $request->title,
            'description'  => $request->description,
            'specialities' => $rows,
            'created_by'   => Auth::id(),
            'created_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('home-specialities.index')
            ->with('message', 'Specialities added successfully.');
    }

    public function edit($id)
    {
        $speciality = HomeSpecialities::findOrFail($id);
        return view('backend.home.specialities.edit', compact('speciality'));
    }

    public function update(Request $request, $id)
    {
        $speciality = HomeSpecialities::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'our_motto'        => 'required|string|max:255',
                'title'            => 'required|string|max:255',
                'description'      => 'required|string',

                'names'            => 'required|array|min:1',
                'names.*'          => 'required|string|max:255',

                // icons array entries can be missing when row keeps its existing icon
                'icons'            => 'array',
                'icons.*'          => 'nullable|file|image|mimes:jpg,jpeg,png,webp,svg|max:2048',

                'existing_icons'   => 'array',
                'existing_icons.*' => 'nullable|string',
            ],
            [
                'our_motto.required'   => 'Please enter Our Motto.',
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',

                'names.required'   => 'Please add at least one speciality.',
                'names.*.required' => 'Speciality name is required.',
                'names.*.max'      => 'Speciality name must be 255 characters or less.',

                'icons.*.image' => 'Icon must be an image.',
                'icons.*.mimes' => 'Allowed icon formats: jpg, jpeg, png, webp, svg.',
                'icons.*.max'   => 'Each icon must be 2MB or smaller.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Custom Validation — every row must have either a new icon or an existing one
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
                    $validator->errors()->add("icons.$idx", 'Row '.((int) $idx + 1).': icon is required.');
                }
            }
        });

        $validator->validate();

        /*
        |--------------------------------------------------------------------------
        | Build the new rows; replace icons on disk where a new file was uploaded;
        | track which old icons are no longer in use so we can clean them up.
        |--------------------------------------------------------------------------
        */
        $folder = public_path('home/specialities');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $oldRows  = $speciality->specialities ?? [];
        $oldIcons = array_filter(array_column($oldRows, 'icon'));

        $rows     = [];
        $names    = $request->input('names', []);
        $icons    = $request->file('icons', []);
        $existing = $request->input('existing_icons', []);

        foreach ($names as $idx => $name) {

            $newFile = $icons[$idx] ?? null;
            $oldName = $existing[$idx] ?? null;

            if ($newFile) {
                // Uploaded a replacement → save new file, mark old one for deletion
                $fileName = time().'_'.uniqid().'.'.$newFile->getClientOriginalExtension();
                $newFile->move($folder, $fileName);

                // If the row previously had an existing icon, queue it for removal
                if ($oldName && file_exists($folder.'/'.$oldName)) {
                    @unlink($folder.'/'.$oldName);
                }

                $finalIcon = $fileName;
            } else {
                // Keep the existing icon
                $finalIcon = $oldName;
            }

            $rows[] = [
                'icon' => $finalIcon,
                'name' => $name,
            ];
        }

        // Delete any icons that were on the old record but are not referenced anymore
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
        $speciality->update([
            'our_motto'    => $request->our_motto,
            'title'        => $request->title,
            'description'  => $request->description,
            'specialities' => $rows,
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('home-specialities.index')
            ->with('message', 'Specialities updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = HomeSpecialities::findOrFail($id);
            $industries->update($data);

            return redirect()->route('home-specialities.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }

}
