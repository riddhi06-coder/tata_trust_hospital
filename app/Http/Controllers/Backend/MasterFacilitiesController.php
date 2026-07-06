<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FacilitySetting;
use App\Models\MasterFacility;

class MasterFacilitiesController extends Controller
{
    /**
     * Directory (under /public) where facility & banner images are stored.
     */
    private string $folder = 'home/master-facilities';

    public function index()
    {
        $facilities = MasterFacility::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('backend.facility.index', compact('facilities'));
    }

    public function create()
    {
        $hasItems   = MasterFacility::whereNull('deleted_by')->exists();
        $showBanner = ! $hasItems;
        $settings   = $showBanner ? FacilitySetting::whereNull('deleted_by')->first() : null;

        return view('backend.facility.create', compact('showBanner', 'settings'));
    }

    public function store(Request $request)
    {
        $hasItems   = MasterFacility::whereNull('deleted_by')->exists();
        $showBanner = ! $hasItems;

        $rules = [
            'name'        => 'required|string|max:255',
            'image'       => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'required|string',
        ];

        if ($showBanner) {
            $rules['banner_heading']      = 'nullable|string|max:255';
            $rules['banner_image']        = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['section_heading']     = 'nullable|string|max:255';
            $rules['section_description']  = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.required'        => 'Please enter Facility name.',
            'image.required'       => 'Please upload a Facility image.',
            'image.image'          => 'Facility file must be an image.',
            'image.mimes'          => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'            => 'Facility image must be 2MB or smaller.',
            'description.required' => 'Please enter Facility description.',
            'banner_image.image'   => 'Banner file must be an image.',
            'banner_image.mimes'   => 'Allowed banner formats: jpg, jpeg, png, webp.',
            'banner_image.max'     => 'Banner image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder = public_path($this->folder);
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        MasterFacility::create([
            'name'        => $request->name,
            'image'       => $fileName,
            'description' => $request->description,
            'created_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-master-facilities.index')
            ->with('message', 'Facility added successfully.');
    }

    public function edit($id)
    {
        $item       = MasterFacility::findOrFail($id);
        $firstId    = MasterFacility::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);
        $settings   = $showBanner ? FacilitySetting::whereNull('deleted_by')->first() : null;

        return view('backend.facility.edit', compact('item', 'showBanner', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $item       = MasterFacility::findOrFail($id);
        $firstId    = MasterFacility::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $item->id === (int) $firstId);

        $rules = [
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'required|string',
        ];

        if ($showBanner) {
            $rules['banner_heading']      = 'nullable|string|max:255';
            $rules['banner_image']        = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['section_heading']     = 'nullable|string|max:255';
            $rules['section_description']  = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.required'        => 'Please enter Facility name.',
            'image.image'          => 'Facility file must be an image.',
            'image.mimes'          => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'            => 'Facility image must be 2MB or smaller.',
            'description.required' => 'Please enter Facility description.',
            'banner_image.image'   => 'Banner file must be an image.',
            'banner_image.mimes'   => 'Allowed banner formats: jpg, jpeg, png, webp.',
            'banner_image.max'     => 'Banner image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder   = public_path($this->folder);
        $fileName = $item->image;

        if ($request->hasFile('image')) {
            if (! file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (! empty($item->image) && file_exists($folder.'/'.$item->image)) {
                @unlink($folder.'/'.$item->image);
            }
            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        $item->update([
            'name'        => $request->name,
            'image'       => $fileName,
            'description' => $request->description,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-master-facilities.index')
            ->with('message', 'Facility updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $item = MasterFacility::findOrFail($id);
            $item->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-master-facilities.index')
                ->with('message', 'Facility deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /**
     * Create/update the single banner + section-heading settings row.
     */
    private function saveSectionSettings(Request $request, string $folder): void
    {
        $settings = FacilitySetting::whereNull('deleted_by')->first();

        $bannerImageName = $settings->banner_image ?? null;

        if ($request->hasFile('banner_image')) {
            if (! empty($bannerImageName) && file_exists($folder.'/'.$bannerImageName)) {
                @unlink($folder.'/'.$bannerImageName);
            }
            $file            = $request->file('banner_image');
            $bannerImageName = time().'_'.uniqid().'_banner.'.$file->getClientOriginalExtension();
            $file->move($folder, $bannerImageName);
        }

        $data = [
            'banner_heading'      => $request->banner_heading,
            'banner_image'        => $bannerImageName,
            'section_heading'     => $request->section_heading,
            'section_description' => $request->section_description,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            FacilitySetting::create($data);
        }
    }
}
