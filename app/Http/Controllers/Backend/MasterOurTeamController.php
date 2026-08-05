<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OurTeam;
use App\Models\OurTeamSetting;


class MasterOurTeamController extends Controller
{

    public function index()
    {
        $members = OurTeam::whereNull('deleted_by')->orderByDesc('id')->get();

        return view('backend.our_team.index', compact('members'));
    }

    public function create()
    {
        $hasItems   = OurTeam::whereNull('deleted_by')->exists();
        $showBanner = ! $hasItems;
        $settings   = $showBanner ? OurTeamSetting::whereNull('deleted_by')->first() : null;

        return view('backend.our_team.create', compact('showBanner', 'settings'));
    }

    public function store(Request $request)
    {
        $showBanner = ! OurTeam::whereNull('deleted_by')->exists();

        $rules = [
            'name'              => 'required|string|max:255',
            'designation'       => 'required|string|max:2000',
            'education'         => 'nullable|string|max:500',
            'bio'               => 'nullable|string',
            'social_media_link' => 'nullable|url|max:1000',
            'image'             => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'show_on_team_page' => 'nullable|boolean',
        ];

        if ($showBanner) {
            $rules += $this->settingsRules();
        }

        $validator = Validator::make($request->all(), $rules, $this->validationMessages());

        $validator->validate();

        $folder = public_path('our-team');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSettings($request, $folder);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        OurTeam::create([
            'name'              => $request->name,
            'slug'              => $this->generateUniqueSlug($request->name),
            'designation'       => $request->designation,
            'education'         => $request->education,
            'bio'               => $request->bio,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'show_on_team_page' => (bool) $request->boolean('show_on_team_page'),
            'created_by'        => Auth::id(),
            'created_at'        => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-our-team.index')
            ->with('message', 'Team member added successfully.');
    }

    public function edit($id)
    {
        $member     = OurTeam::findOrFail($id);
        $topId      = OurTeam::whereNull('deleted_by')->max('id');
        $showBanner = ((int) $member->id === (int) $topId);
        $settings   = $showBanner ? OurTeamSetting::whereNull('deleted_by')->first() : null;

        return view('backend.our_team.edit', compact('member', 'showBanner', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $member = OurTeam::findOrFail($id);

        $topId      = OurTeam::whereNull('deleted_by')->max('id');
        $showBanner = ((int) $member->id === (int) $topId);

        $rules = [
            'name'              => 'required|string|max:255',
            'designation'       => 'required|string|max:2000',
            'education'         => 'nullable|string|max:500',
            'bio'               => 'nullable|string',
            'social_media_link' => 'nullable|url|max:1000',
            'image'             => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'show_on_team_page' => 'nullable|boolean',
        ];

        if ($showBanner) {
            $rules += $this->settingsRules();
        }

        $validator = Validator::make($request->all(), $rules, $this->validationMessages());

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

        if ($showBanner) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            $this->saveSettings($request, $folder);
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
            'bio'               => $request->bio,
            'social_media_link' => $request->social_media_link,
            'image'             => $fileName,
            'show_on_team_page' => (bool) $request->boolean('show_on_team_page'),
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

    /*
    |--------------------------------------------------------------------------
    | First-record page settings (banner / section / motto / board)
    |--------------------------------------------------------------------------
    */

    /** Validation rules for the one-time page settings (first record only). */
    private function settingsRules(): array
    {
        return [
            'banner_heading'      => 'nullable|string|max:255',
            'banner_image'        => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'section_heading'     => 'nullable|string|max:255',
            'section_description' => 'nullable|string',

            'motto'               => 'nullable|string|max:255',
            'motto_description'   => 'nullable|string',
            'motto_image'         => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',

            'board_heading'       => 'nullable|string|max:255',
            'board_small_desc'    => 'nullable|string',
            'board_image'         => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'board_name'          => 'nullable|string|max:255',
            'board_designation'   => 'nullable|string|max:255',
            'board_members'       => 'nullable|array',
            'board_members.*'     => 'nullable|string|max:255',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'name.required'         => 'Please enter Member Name.',
            'name.max'              => 'Member Name must be 255 characters or less.',
            'designation.required'  => 'Please enter Designation.',
            'designation.max'       => 'Designation is too long.',
            'education.max'         => 'Education must be 500 characters or less.',
            'social_media_link.url' => 'Social Media Link must be a valid URL.',
            'social_media_link.max' => 'Social Media Link must be 1000 characters or less.',
            'image.required'        => 'Please upload an image.',
            'image.image'           => 'File must be an image.',
            'image.mimes'           => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'             => 'Image must be 2MB or smaller.',
            'banner_image.max'      => 'Banner image must be 2MB or smaller.',
            'motto_image.max'       => 'Motto image must be 2MB or smaller.',
            'board_image.max'       => 'Board image must be 2MB or smaller.',
        ];
    }

    /** Create/update the single settings row (banner, section, motto, board). */
    private function saveSettings(Request $request, string $folder): void
    {
        $settings = OurTeamSetting::whereNull('deleted_by')->first();

        $bannerImage = $this->handleSettingImage($request, 'banner_image', $folder, $settings->banner_image ?? null, '_banner');
        $mottoImage  = $this->handleSettingImage($request, 'motto_image',  $folder, $settings->motto_image  ?? null, '_motto');
        $boardImage  = $this->handleSettingImage($request, 'board_image',  $folder, $settings->board_image  ?? null, '_board');

        // Board member names — drop blank rows, keep order.
        $boardMembers = array_values(array_filter(
            array_map(
                fn ($n) => is_string($n) ? trim($n) : '',
                $request->input('board_members', [])
            ),
            fn ($n) => $n !== ''
        ));

        $data = [
            'banner_heading'      => $request->banner_heading,
            'banner_image'        => $bannerImage,
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
            'motto'               => $request->motto,
            'motto_description'   => $request->motto_description,
            'motto_image'         => $mottoImage,
            'board_heading'       => $request->board_heading,
            'board_small_desc'    => $request->board_small_desc,
            'board_image'         => $boardImage,
            'board_name'          => $request->board_name,
            'board_designation'   => $request->board_designation,
            'board_members'       => $boardMembers,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            OurTeamSetting::create($data);
        }
    }

    /**
     * Move an uploaded settings image (if present), delete the previous one,
     * and return the stored filename. Returns the current filename untouched
     * when no new file was uploaded.
     */
    private function handleSettingImage(Request $request, string $field, string $folder, ?string $current, string $suffix): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if (!empty($current) && file_exists($folder.'/'.$current)) {
            @unlink($folder.'/'.$current);
        }

        $file = $request->file($field);
        $name = time().'_'.uniqid().$suffix.'.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);

        return $name;
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
