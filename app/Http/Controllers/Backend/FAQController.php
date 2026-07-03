<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\FaqSetting;

class FAQController extends Controller
{
    public function index()
    {
        $faqs = Faq::whereNull('deleted_by')->orderBy('id')->get();

        return view('backend.faq.index', compact('faqs'));
    }

    public function create()
    {
        $hasFaqs    = Faq::whereNull('deleted_by')->exists();
        $showBanner = ! $hasFaqs;
        $settings   = $showBanner ? FaqSetting::whereNull('deleted_by')->first() : null;

        return view('backend.faq.create', compact('showBanner', 'settings'));
    }

    public function store(Request $request)
    {
        $hasFaqs    = Faq::whereNull('deleted_by')->exists();
        $showBanner = ! $hasFaqs;

        $rules = [
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
        ];

        if ($showBanner) {
            $rules['banner_heading']  = 'nullable|string|max:255';
            $rules['banner_image']    = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['section_heading'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules, [
            'question.required'   => 'Please enter a Question.',
            'answer.required'     => 'Please enter an Answer.',
            'banner_image.image'  => 'Banner Image must be an image.',
            'banner_image.mimes'  => 'Allowed formats: jpg, jpeg, png, webp.',
            'banner_image.max'    => 'Banner Image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder = public_path('home/faq');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        Faq::create([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-faqs.index')
            ->with('message', 'FAQ added successfully.');
    }

    public function edit($id)
    {
        $faq        = Faq::findOrFail($id);
        $firstId    = Faq::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $faq->id === (int) $firstId);
        $settings   = $showBanner ? FaqSetting::whereNull('deleted_by')->first() : null;

        return view('backend.faq.edit', compact('faq', 'showBanner', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $faq        = Faq::findOrFail($id);
        $firstId    = Faq::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $faq->id === (int) $firstId);

        $rules = [
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
        ];

        if ($showBanner) {
            $rules['banner_heading']  = 'nullable|string|max:255';
            $rules['banner_image']    = 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240';
            $rules['section_heading'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules, [
            'question.required'   => 'Please enter a Question.',
            'answer.required'     => 'Please enter an Answer.',
            'banner_image.image'  => 'Banner Image must be an image.',
            'banner_image.mimes'  => 'Allowed formats: jpg, jpeg, png, webp.',
            'banner_image.max'    => 'Banner Image must be 10MB or smaller.',
        ]);

        $validator->validate();

        $folder = public_path('home/faq');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request, $folder);
        }

        $faq->update([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-faqs.index')
            ->with('message', 'FAQ updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-faqs.index')
                ->with('message', 'FAQ deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    private function saveSectionSettings(Request $request, string $folder): void
    {
        $settings = FaqSetting::whereNull('deleted_by')->first();

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
            'banner_heading'  => $request->banner_heading,
            'banner_image'    => $bannerImageName,
            'section_heading' => $request->section_heading,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            FaqSetting::create($data);
        }
    }
}
