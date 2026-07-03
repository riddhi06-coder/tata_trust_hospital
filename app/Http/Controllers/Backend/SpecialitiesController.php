<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SpecialitySetting;
use App\Models\Specialities;

class SpecialitiesController extends Controller
{
    public function index()
    {
        $specialities = Specialities::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('backend.specialities.index', compact('specialities'));
    }

    public function create()
    {
        $hasItems   = Specialities::whereNull('deleted_by')->exists();
        $showBanner = ! $hasItems;
        $settings   = $showBanner ? SpecialitySetting::whereNull('deleted_by')->first() : null;

        return view('backend.specialities.create', compact('showBanner', 'settings'));
    }

    public function store(Request $request)
    {
        $hasItems   = Specialities::whereNull('deleted_by')->exists();
        $showBanner = ! $hasItems;

        $rules = [
            'speciality' => 'required|string|max:255',
            'image'      => 'required|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        if ($showBanner) {
            $rules['banner_heading']          = 'nullable|string|max:255';
            $rules['banner_image']            = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['service_section_heading'] = 'nullable|string|max:255';
            $rules['service_description']     = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'speciality.required' => 'Please enter Speciality name.',
            'image.required'      => 'Please upload a Speciality image.',
            'image.image'         => 'Speciality file must be an image.',
            'image.mimes'         => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'           => 'Speciality image must be 5MB or smaller.',
            'banner_image.image'  => 'Banner file must be an image.',
            'banner_image.mimes'  => 'Allowed banner formats: jpg, jpeg, png, webp.',
            'banner_image.max'    => 'Banner image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder = public_path('home/specialities');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        Specialities::create([
            'speciality' => $request->speciality,
            'slug'       => $this->generateUniqueSlug($request->speciality),
            'image'      => $fileName,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-specialities.index')
            ->with('message', 'Speciality added successfully.');
    }

    public function edit($id)
    {
        $item       = Specialities::findOrFail($id);
        $firstId    = Specialities::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);
        $settings   = $showBanner ? SpecialitySetting::whereNull('deleted_by')->first() : null;

        return view('backend.specialities.edit', compact('item', 'showBanner', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $item       = Specialities::findOrFail($id);
        $firstId    = Specialities::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);

        $rules = [
            'speciality' => 'required|string|max:255',
            'image'      => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        if ($showBanner) {
            $rules['banner_heading']          = 'nullable|string|max:255';
            $rules['banner_image']            = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['service_section_heading'] = 'nullable|string|max:255';
            $rules['service_description']     = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'speciality.required' => 'Please enter Speciality name.',
            'image.image'         => 'Speciality file must be an image.',
            'image.mimes'         => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'           => 'Speciality image must be 5MB or smaller.',
            'banner_image.image'  => 'Banner file must be an image.',
            'banner_image.mimes'  => 'Allowed banner formats: jpg, jpeg, png, webp.',
            'banner_image.max'    => 'Banner image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder   = public_path('home/specialities');
        $fileName = $item->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (!empty($item->image) && file_exists($folder.'/'.$item->image)) {
                @unlink($folder.'/'.$item->image);
            }
            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        $slug = $item->slug;
        if ($request->speciality !== $item->speciality || empty($slug)) {
            $slug = $this->generateUniqueSlug($request->speciality, $item->id);
        }

        $item->update([
            'speciality' => $request->speciality,
            'slug'       => $slug,
            'image'      => $fileName,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-specialities.index')
            ->with('message', 'Speciality updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $item = Specialities::findOrFail($id);
            $item->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-specialities.index')
                ->with('message', 'Speciality deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'speciality';
        }
        $slug = $base;
        $i    = 1;
        while (
            Specialities::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }

    private function saveSectionSettings(Request $request, string $folder): void
    {
        $settings = SpecialitySetting::whereNull('deleted_by')->first();

        $bannerImageName = $settings->banner_image ?? null;

        if ($request->hasFile('banner_image')) {
            if (!empty($bannerImageName) && file_exists($folder.'/'.$bannerImageName)) {
                @unlink($folder.'/'.$bannerImageName);
            }
            $file            = $request->file('banner_image');
            $bannerImageName = time().'_'.uniqid().'_banner.'.$file->getClientOriginalExtension();
            $file->move($folder, $bannerImageName);
        }

        $data = [
            'banner_heading'          => $request->banner_heading,
            'banner_image'            => $bannerImageName,
            'service_section_heading' => $request->service_section_heading,
            'service_description'     => $request->service_description,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            SpecialitySetting::create($data);
        }
    }
}
