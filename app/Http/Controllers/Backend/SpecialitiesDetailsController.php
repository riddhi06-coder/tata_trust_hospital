<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OurTeam;
use App\Models\Specialities;
use App\Models\SpecialitiesDetails;

class SpecialitiesDetailsController extends Controller
{
    private const FILE_DIR = 'home/speciality-details';

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
        $doctors      = OurTeam::whereNull('deleted_by')->orderBy('name')->get();

        return view('backend.specialities.details.create', compact('specialities', 'doctors'));
    }

    public function store(Request $request)
    {
        $isPreventive = $this->isPreventiveSpeciality($request->speciality_id);

        $this->validateDetail($request, $isPreventive, false);

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        $data = [
            'speciality_id'       => $request->speciality_id,
            'is_preventive'       => $isPreventive,
            'banner_image'        => $this->moveUploaded($request->file('banner_image'), $folder, 'banner'),
            'section_image'       => $this->moveUploaded($request->file('section_image'), $folder, 'section'),
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
            'created_by'          => Auth::id(),
            'created_at'          => Carbon::now(),
        ];

        if ($isPreventive) {
            $data += [
                'preventive_section_heading'     => $request->preventive_section_heading,
                'preventive_section_description' => $request->preventive_section_description,
                'preventive_services'            => $this->buildServiceRows($request, $folder),
                'preventive_plans_heading'       => $request->preventive_plans_heading,
                'preventive_plans_description'   => $request->preventive_plans_description,
                'preventive_plans'               => $this->buildPlanRows($request, $folder),
                'preventive_disclaimer'          => $request->preventive_disclaimer,
            ];
        } else {
            $data += [
                'service_heading' => $request->service_heading,
                'services'        => $this->cleanServices($request),
                'short_info'      => $request->short_info,
            ];
        }

        $detail = SpecialitiesDetails::create($data);

        if (! $isPreventive) {
            $this->syncDoctors($detail, $request);
        }

        return redirect()
            ->route('speciality-details.index')
            ->with('message', 'Speciality Details added successfully.');
    }

    public function edit($id)
    {
        $detail       = SpecialitiesDetails::with('doctors')->findOrFail($id);
        $specialities = Specialities::whereNull('deleted_by')->orderBy('speciality')->get();
        $doctors      = OurTeam::whereNull('deleted_by')->orderBy('name')->get();

        return view('backend.specialities.details.edit', compact('detail', 'specialities', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        $detail = SpecialitiesDetails::findOrFail($id);

        $isPreventive = $this->isPreventiveSpeciality($request->speciality_id);

        $this->validateDetail($request, $isPreventive, true);

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        $bannerName = $detail->banner_image;
        if ($request->hasFile('banner_image')) {
            $this->deleteFile($folder, $detail->banner_image);
            $bannerName = $this->moveUploaded($request->file('banner_image'), $folder, 'banner');
        }

        $sectionName = $detail->section_image;
        if ($request->hasFile('section_image')) {
            $this->deleteFile($folder, $detail->section_image);
            $sectionName = $this->moveUploaded($request->file('section_image'), $folder, 'section');
        }

        $data = [
            'speciality_id'       => $request->speciality_id,
            'is_preventive'       => $isPreventive,
            'banner_image'        => $bannerName,
            'section_image'       => $sectionName,
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
            'updated_by'          => Auth::id(),
            'updated_at'          => Carbon::now(),
        ];

        if ($isPreventive) {
            $data += [
                'preventive_section_heading'     => $request->preventive_section_heading,
                'preventive_section_description' => $request->preventive_section_description,
                'preventive_services'            => $this->buildServiceRows($request, $folder),
                'preventive_plans_heading'       => $request->preventive_plans_heading,
                'preventive_plans_description'   => $request->preventive_plans_description,
                'preventive_plans'               => $this->buildPlanRows($request, $folder),
                'preventive_disclaimer'          => $request->preventive_disclaimer,
            ];
        } else {
            $data += [
                'service_heading' => $request->service_heading,
                'services'        => $this->cleanServices($request),
                'short_info'      => $request->short_info,
            ];
        }

        $detail->update($data);

        if ($isPreventive) {
            $detail->doctors()->sync([]); // preventive layout has no attached doctors
        } else {
            $this->syncDoctors($detail, $request);
        }

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

    /* ===================================================================== */

    /** A speciality is treated as "Preventive Care" when its name/slug says so. */
    private function isPreventiveSpeciality($specialityId): bool
    {
        $spec = Specialities::find($specialityId);
        if (! $spec) {
            return false;
        }
        $hay = strtolower(trim(($spec->speciality ?? '').' '.($spec->slug ?? '')));

        return str_contains($hay, 'preventive');
    }

    private function validateDetail(Request $request, bool $isPreventive, bool $isUpdate): void
    {
        $bannerRule = ($isUpdate ? 'nullable' : 'required').'|file|image|mimes:jpg,jpeg,png,webp|max:10240';

        if ($isPreventive) {
            $validator = Validator::make($request->all(), [
                'speciality_id'                  => 'required|exists:specialities,id',
                'banner_image'                   => $bannerRule,
                'section_image'                  => ($isUpdate ? 'nullable' : 'required').'|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'section_heading'                => 'required|string|max:255',
                'section_description'            => 'required|string',
                'preventive_section_heading'     => 'required|string|max:255',
                'preventive_section_description' => 'required|string',
                'preventive_service_name'        => 'required|array|min:1',
                'preventive_service_name.*'      => 'required|string|max:255',
                'preventive_service_image.*'     => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240',
                'preventive_plans_heading'       => 'required|string|max:255',
                'preventive_plans_description'   => 'required|string',
                'plan_category'                  => 'required|array|min:1',
                'plan_category.*'                => 'required|string|max:255',
                'preventive_disclaimer'          => 'required|string',
            ], [
                'speciality_id.required'                  => 'Please select a Speciality.',
                'banner_image.required'                   => 'Please upload a Banner Image.',
                'section_image.required'                  => 'Please upload a Section Image.',
                'section_heading.required'                => 'Please enter Section Heading.',
                'section_description.required'            => 'Please enter Section Description.',
                'preventive_section_heading.required'     => 'Please enter the Preventive section heading.',
                'preventive_section_description.required' => 'Please enter the Preventive section description.',
                'preventive_service_name.required'        => 'Please add at least one service included.',
                'preventive_service_name.min'             => 'Please add at least one service included.',
                'preventive_service_name.*.required'      => 'Each service needs a name.',
                'preventive_plans_heading.required'       => 'Please enter the Preventive Care Plans heading.',
                'preventive_plans_description.required'   => 'Please enter the Preventive Care Plans description.',
                'plan_category.required'                  => 'Please add at least one plans category.',
                'plan_category.min'                       => 'Please add at least one plans category.',
                'plan_category.*.required'                => 'Each plans category needs a name.',
                'preventive_disclaimer.required'          => 'Please enter the disclaimer.',
            ]);

            // Service rows each need an image; plan categories each need complete rows.
            $validator->after(function ($v) use ($request, $isUpdate) {
                $this->requireRowImages($v, $request, $isUpdate, 'preventive_service_name', 'preventive_service_image', 'preventive_service_existing', 'service');
                $this->validatePlanCategories($v, $request, $isUpdate);
            });

            $validator->validate();
            return;
        }

        // Normal (non-preventive) layout.
        Validator::make($request->all(), [
            'speciality_id'       => 'required|exists:specialities,id',
            'banner_image'        => $bannerRule,
            'section_image'       => ($isUpdate ? 'nullable' : 'required').'|file|image|mimes:jpg,jpeg,png,webp|max:10240',
            'section_heading'     => 'required|string|max:255',
            'section_description' => 'required|string',
            'service_heading'     => 'required|string|max:255',
            'services'            => 'required|array|min:1',
            'services.*'          => 'required|string|max:2000',
            'short_info'          => 'nullable|string|max:2000',
            'doctor_ids'          => 'nullable|array',
            'doctor_ids.*'        => 'nullable|integer|exists:our_teams,id',
            'doctor_bio_override' => 'nullable|array',
            'doctor_bio_override.*' => 'nullable|string',
        ], [
            'speciality_id.required'       => 'Please select a Speciality.',
            'banner_image.required'        => 'Please upload a Banner Image.',
            'section_image.required'       => 'Please upload a Section Image.',
            'section_heading.required'     => 'Please enter Section Heading.',
            'section_description.required' => 'Please enter Section Description.',
            'service_heading.required'     => 'Please enter Service Heading.',
            'services.required'            => 'Please add at least one service description.',
            'services.min'                 => 'Please add at least one service description.',
            'services.*.required'          => 'Each service description is required.',
            'services.*.max'               => 'Each service description must be 2000 characters or less.',
        ])->validate();
    }

    /** Adds a validation error for any named row that has neither a new nor an existing image. */
    private function requireRowImages($validator, Request $request, bool $isUpdate, string $nameKey, string $fileKey, string $existingKey, string $label): void
    {
        $names    = (array) $request->input($nameKey, []);
        $existing = (array) $request->input($existingKey, []);

        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $hasNew = $request->file("{$fileKey}.{$i}") !== null;
            $hasOld = $isUpdate && ! empty($existing[$i]);
            if (! $hasNew && ! $hasOld) {
                $validator->errors()->add("{$fileKey}.{$i}", 'Please upload an image for '.$label.' row '.($i + 1).'.');
            }
        }
    }

    /** Build the preventive "services included" rows: [{image, name}]. */
    private function buildServiceRows(Request $request, string $folder): array
    {
        $names    = (array) $request->input('preventive_service_name', []);
        $existing = (array) $request->input('preventive_service_existing', []);
        $rows     = [];

        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $image = ! empty($existing[$i]) ? $existing[$i] : null;
            $file  = $request->file("preventive_service_image.{$i}");
            if ($file) {
                $this->deleteFile($folder, $image);
                $image = $this->moveUploaded($file, $folder, 'pservice');
            }
            $rows[] = ['image' => $image, 'name' => trim((string) $name)];
        }

        return $rows;
    }

    /**
     * Build the preventive "care plans" as category groups:
     * [{category, plans: [{image, name, age_range, cost}]}].
     * Inputs are nested by category block index: plan_name[b][], plan_image[b][], ...
     */
    private function buildPlanRows(Request $request, string $folder): array
    {
        $categories = (array) $request->input('plan_category', []);
        $result     = [];

        foreach ($categories as $b => $catName) {
            $catName  = trim((string) $catName);
            $names    = (array) $request->input("plan_name.{$b}", []);
            $ranges   = (array) $request->input("plan_age_range.{$b}", []);
            $costs    = (array) $request->input("plan_cost.{$b}", []);
            $existing = (array) $request->input("plan_existing.{$b}", []);

            $plans = [];
            foreach ($names as $r => $name) {
                if (trim((string) $name) === '') {
                    continue;
                }
                $image = ! empty($existing[$r]) ? $existing[$r] : null;
                $file  = $request->file("plan_image.{$b}.{$r}");
                if ($file) {
                    $this->deleteFile($folder, $image);
                    $image = $this->moveUploaded($file, $folder, 'pplan');
                }
                $plans[] = [
                    'image'     => $image,
                    'name'      => trim((string) $name),
                    'age_range' => trim((string) ($ranges[$r] ?? '')),
                    'cost'      => trim((string) ($costs[$r] ?? '')),
                ];
            }

            if ($catName === '' && empty($plans)) {
                continue;
            }
            $result[] = ['category' => $catName, 'plans' => $plans];
        }

        return $result;
    }

    /** Each plan category needs at least one complete row (name, age range, cost, image). */
    private function validatePlanCategories($validator, Request $request, bool $isUpdate): void
    {
        $categories = (array) $request->input('plan_category', []);
        $n = 0;
        foreach ($categories as $b => $catName) {
            $n++;
            $label = trim((string) $catName) !== '' ? '"'.trim((string) $catName).'"' : 'Category '.$n;

            $names    = (array) $request->input("plan_name.{$b}", []);
            $ranges   = (array) $request->input("plan_age_range.{$b}", []);
            $costs    = (array) $request->input("plan_cost.{$b}", []);
            $existing = (array) $request->input("plan_existing.{$b}", []);

            $hasRow = false;
            foreach ($names as $r => $name) {
                if (trim((string) $name) === '') {
                    continue;
                }
                $hasRow = true;
                $hasImg = ($request->file("plan_image.{$b}.{$r}") !== null) || ($isUpdate && ! empty($existing[$r]));
                if (trim((string) ($ranges[$r] ?? '')) === '' || trim((string) ($costs[$r] ?? '')) === '' || ! $hasImg) {
                    $validator->errors()->add("plan_row.{$b}.{$r}", $label.': each plan needs a name, age range, cost and image.');
                }
            }

            if (! $hasRow) {
                $validator->errors()->add("plan_rows.{$b}", $label.': add at least one plan (name, age range, cost, image).');
            }
        }
    }

    private function cleanServices(Request $request): array
    {
        return array_values(array_filter(
            (array) $request->input('services', []),
            fn ($s) => is_string($s) && trim($s) !== ''
        ));
    }

    private function syncDoctors(SpecialitiesDetails $detail, Request $request): void
    {
        $doctorIds    = (array) $request->input('doctor_ids', []);
        $bioOverrides = (array) $request->input('doctor_bio_override', []);

        $sync  = [];
        $order = 0;
        foreach ($doctorIds as $index => $ourTeamId) {
            $ourTeamId = (int) $ourTeamId;
            if ($ourTeamId <= 0) {
                continue;
            }
            $sync[$ourTeamId] = [
                'bio_override' => isset($bioOverrides[$index]) && trim($bioOverrides[$index]) !== ''
                    ? $bioOverrides[$index]
                    : null,
                'sort_order'   => $order++,
            ];
        }

        $detail->doctors()->sync($sync);
    }

    private function ensureFolder(string $folder): void
    {
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    private function moveUploaded($file, string $folder, string $suffix): ?string
    {
        if (! $file) {
            return null;
        }
        $name = time().'_'.uniqid().'_'.$suffix.'.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);
        return $name;
    }

    private function deleteFile(string $folder, ?string $name): void
    {
        if (! empty($name) && file_exists($folder.'/'.$name)) {
            @unlink($folder.'/'.$name);
        }
    }
}
