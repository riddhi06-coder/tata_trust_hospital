<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\EventSetting;
use App\Models\Events;

class EventsController extends Controller
{
    public function index()
    {
        $events = Events::whereNull('deleted_by')
            ->orderBy('id')
            ->get();

        return view('backend.events.index', compact('events'));
    }

    public function create()
    {
        $hasEvents  = Events::whereNull('deleted_by')->exists();
        $showBanner = ! $hasEvents;
        $settings   = $showBanner ? EventSetting::whereNull('deleted_by')->first() : null;
        $months     = Events::MONTHS;

        return view('backend.events.create', compact('showBanner', 'settings', 'months'));
    }

    public function store(Request $request)
    {
        $hasEvents  = Events::whereNull('deleted_by')->exists();
        $showBanner = ! $hasEvents;

        $rules = [
            'title'     => 'required|string|max:255',
            'month'     => 'nullable|integer|min:1|max:12',
            'thumbnail' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image'     => 'required|file|image|mimes:jpg,jpeg,png,webp|max:10240',
        ];

        if ($showBanner) {
            $rules['section_heading'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules, [
            'title.required'     => 'Please enter Event Title.',
            'thumbnail.required' => 'Please upload a Thumbnail.',
            'thumbnail.image'    => 'Thumbnail must be an image.',
            'thumbnail.mimes'    => 'Allowed thumbnail formats: jpg, jpeg, png, webp.',
            'thumbnail.max'      => 'Thumbnail must be 5MB or smaller.',
            'image.required'     => 'Please upload an Event Image.',
            'image.image'        => 'Event Image must be an image.',
            'image.mimes'        => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'          => 'Event Image must be 10MB or smaller.',
            'month.integer'      => 'Please choose a valid month.',
            'month.min'          => 'Please choose a valid month.',
            'month.max'          => 'Please choose a valid month.',
        ]);

        $validator->validate();

        $folder = public_path('home/events');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request);
        }

        $thumbFile = $request->file('thumbnail');
        $thumbName = time().'_'.uniqid().'_thumb.'.$thumbFile->getClientOriginalExtension();
        $thumbFile->move($folder, $thumbName);

        $imgFile = $request->file('image');
        $imgName = time().'_'.uniqid().'_img.'.$imgFile->getClientOriginalExtension();
        $imgFile->move($folder, $imgName);

        Events::create([
            'title'      => $request->title,
            'thumbnail'  => $thumbName,
            'image'      => $imgName,
            'month'      => $request->month,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-events.index')
            ->with('message', 'Event added successfully.');
    }

    public function edit($id)
    {
        $event      = Events::findOrFail($id);
        $firstId    = Events::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $event->id === (int) $firstId);
        $settings   = $showBanner ? EventSetting::whereNull('deleted_by')->first() : null;
        $months     = Events::MONTHS;

        return view('backend.events.edit', compact('event', 'showBanner', 'settings', 'months'));
    }

    public function update(Request $request, $id)
    {
        $event      = Events::findOrFail($id);
        $firstId    = Events::whereNull('deleted_by')->min('id');
        $showBanner = ((int) $event->id === (int) $firstId);

        $rules = [
            'title'        => 'required|string|max:255',
            'month'        => 'nullable|integer|min:1|max:12',
            'thumbnail'    => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image'        => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:10240',
            'show_on_home' => 'nullable|boolean',
        ];

        if ($showBanner) {
            $rules['section_heading'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules, [
            'title.required' => 'Please enter Event Title.',
            'thumbnail.image'=> 'Thumbnail must be an image.',
            'thumbnail.mimes'=> 'Allowed thumbnail formats: jpg, jpeg, png, webp.',
            'thumbnail.max'  => 'Thumbnail must be 5MB or smaller.',
            'image.image'    => 'Event Image must be an image.',
            'image.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp.',
            'image.max'      => 'Event Image must be 10MB or smaller.',
            'month.integer'  => 'Please choose a valid month.',
            'month.min'      => 'Please choose a valid month.',
            'month.max'      => 'Please choose a valid month.',
        ]);

        $validator->validate();

        $folder    = public_path('home/events');
        $thumbName = $event->thumbnail;
        $imgName   = $event->image;

        if ($request->hasFile('thumbnail')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (!empty($event->thumbnail) && file_exists($folder.'/'.$event->thumbnail)) {
                @unlink($folder.'/'.$event->thumbnail);
            }
            $thumbFile = $request->file('thumbnail');
            $thumbName = time().'_'.uniqid().'_thumb.'.$thumbFile->getClientOriginalExtension();
            $thumbFile->move($folder, $thumbName);
        }

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            if (!empty($event->image) && file_exists($folder.'/'.$event->image)) {
                @unlink($folder.'/'.$event->image);
            }
            $imgFile = $request->file('image');
            $imgName = time().'_'.uniqid().'_img.'.$imgFile->getClientOriginalExtension();
            $imgFile->move($folder, $imgName);
        }

        if ($showBanner) {
            $this->saveSectionSettings($request);
        }

        $event->update([
            'title'        => $request->title,
            'month'        => $request->month,
            'thumbnail'    => $thumbName,
            'image'        => $imgName,
            'show_on_home' => (bool) $request->boolean('show_on_home'),
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-events.index')
            ->with('message', 'Event updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $event = Events::findOrFail($id);
            $event->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-events.index')
                ->with('message', 'Event deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    public function toggleHome(Request $request, string $id)
    {
        try {
            $event = Events::findOrFail($id);
            $event->show_on_home = ! $event->show_on_home;
            $event->updated_by   = Auth::id();
            $event->save();

            return response()->json([
                'success'      => true,
                'show_on_home' => $event->show_on_home,
                'message'      => $event->show_on_home
                    ? 'Event is now shown on Home.'
                    : 'Event is no longer shown on Home.',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong - '.$ex->getMessage(),
            ], 500);
        }
    }

    private function saveSectionSettings(Request $request): void
    {
        $settings = EventSetting::whereNull('deleted_by')->first();

        $data = [
            'section_heading' => $request->section_heading,
        ];

        if ($settings) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $settings->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            EventSetting::create($data);
        }
    }
}
