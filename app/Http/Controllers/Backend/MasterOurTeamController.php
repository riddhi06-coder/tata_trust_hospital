<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OurTeam;


class MasterOurTeamController extends Controller
{

    public function index()
    {
        $members = OurTeam::whereNull('deleted_by')->latest()->get();

        return view('backend.our_team.index', compact('members'));
    }

    public function create()
    {
        return view('backend.our_team.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'              => 'required|string|max:255',
                'designation'       => 'required|string|max:255',
                'education'         => 'nullable|string|max:500',
                'social_media_link' => 'required|url|max:1000',
                'image'             => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'name.required'        => 'Please enter Member Name.',
                'name.max'             => 'Member Name must be 255 characters or less.',

                'designation.required' => 'Please enter Designation.',
                'designation.max'      => 'Designation must be 255 characters or less.',

                'education.max'        => 'Education must be 500 characters or less.',

                'social_media_link.required' => 'Please enter Social Media Link.',
                'social_media_link.url'      => 'Social Media Link must be a valid URL.',
                'social_media_link.max'      => 'Social Media Link must be 1000 characters or less.',

                'image.required' => 'Please upload an image.',
                'image.image'    => 'File must be an image.',
                'image.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'      => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder = public_path('our-team');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        OurTeam::create([
            'name'              => $request->name,
            'slug'              => $this->generateUniqueSlug($request->name),
            'designation'       => $request->designation,
            'education'         => $request->education,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'created_by'        => Auth::id(),
            'created_at'        => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-our-team.index')
            ->with('message', 'Team member added successfully.');
    }

    public function edit($id)
    {
        $member = OurTeam::findOrFail($id);
        return view('backend.our_team.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $member = OurTeam::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name'              => 'required|string|max:255',
                'designation'       => 'required|string|max:255',
                'education'         => 'nullable|string|max:500',
                'social_media_link' => 'required|url|max:1000',
                'image'             => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'name.required'        => 'Please enter Member Name.',
                'name.max'             => 'Member Name must be 255 characters or less.',

                'designation.required' => 'Please enter Designation.',
                'designation.max'      => 'Designation must be 255 characters or less.',

                'education.max'        => 'Education must be 500 characters or less.',

                'social_media_link.required' => 'Please enter Social Media Link.',
                'social_media_link.url'      => 'Social Media Link must be a valid URL.',
                'social_media_link.max'      => 'Social Media Link must be 1000 characters or less.',

                'image.image' => 'File must be an image.',
                'image.mimes' => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'   => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder   = public_path('our-team');
        $fileName = $member->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            if (!empty($member->image) && file_exists($folder.'/'.$member->image)) {
                @unlink($folder.'/'.$member->image);
            }

            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        // Regenerate slug only if the name actually changed; keeps existing URLs stable otherwise
        $slug = $member->slug;
        if ($request->name !== $member->name || empty($slug)) {
            $slug = $this->generateUniqueSlug($request->name, $member->id);
        }

        $member->update([
            'name'              => $request->name,
            'slug'              => $slug,
            'designation'       => $request->designation,
            'education'         => $request->education,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'updated_by'        => Auth::id(),
            'updated_at'        => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-our-team.index')
            ->with('message', 'Team member updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $member = OurTeam::findOrFail($id);
            $member->update($data);

            return redirect()->route('manage-our-team.index')->with('message', 'Details deleted successfully!');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }

    /**
     * Toggle the show_on_home flag for a team member.
     * Returns JSON for AJAX consumers.
     */
    public function toggleHome(Request $request, string $id)
    {
        try {
            $member = OurTeam::findOrFail($id);
            $member->show_on_home = ! $member->show_on_home;
            $member->updated_by   = Auth::id();
            $member->save();

            return response()->json([
                'success'      => true,
                'show_on_home' => $member->show_on_home,
                'message'      => $member->show_on_home
                    ? 'Member is now shown on Home.'
                    : 'Member is no longer shown on Home.',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong - ' . $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique slug from the given name. Checks across non-deleted AND
     * soft-deleted rows so the slug never collides with the DB unique index.
     * Pass $ignoreId when regenerating during an update to skip the current row.
     */
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'member';
        }
        $slug = $base;
        $i    = 1;
        while (
            OurTeam::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }
}
