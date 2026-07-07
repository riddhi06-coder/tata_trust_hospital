<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContactDetails;
use App\Models\ContactRibbonItem;
use App\Models\ContactSocialLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactDetailsController extends Controller
{
    private const BANNER_DIR = 'home/contact/banner';
    private const RIBBON_DIR = 'home/contact/ribbon';

    public function index()
    {
        $contact = ContactDetails::whereNull('deleted_by')->first();

        return view('backend.contact_details.index', compact('contact'));
    }

    public function create()
    {
        $existing = ContactDetails::whereNull('deleted_by')->first();
        if ($existing) {
            return redirect()
                ->route('manage-contact-details.edit', $existing->id)
                ->with('message', 'Contact Details already exist. Edit below.');
        }

        return view('backend.contact_details.create', [
            'platforms' => ContactSocialLink::PLATFORMS,
        ]);
    }

    public function store(Request $request)
    {
        if (ContactDetails::whereNull('deleted_by')->exists()) {
            return redirect()
                ->route('manage-contact-details.index')
                ->with('error', 'Contact Details already exist. Delete first before creating a new record.');
        }

        $this->validatePayload($request, false)->validate();

        $bannerDir = public_path(self::BANNER_DIR);
        $ribbonDir = public_path(self::RIBBON_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($ribbonDir);

        DB::transaction(function () use ($request, $bannerDir, $ribbonDir) {
            $bannerImage = $this->uploadFile($request, 'banner_image', $bannerDir, 'banner');

            $contact = ContactDetails::create([
                'banner_heading'   => $request->banner_heading,
                'banner_image'     => $bannerImage,
                'address'          => $request->address,
                'email'            => $request->email,
                'footer_email'     => $request->footer_email,
                'emergency_no'     => $request->emergency_no,
                'join_team_email'  => $request->join_team_email,
                'donate_info'      => $request->donate_info,
                'map_url'          => $request->map_url,
                'iframe_url'       => $request->iframe_url,
                'created_by'       => Auth::id(),
                'created_at'       => Carbon::now(),
            ]);

            $this->createRibbonItems($contact, $request, $ribbonDir);
            $this->createSocialLinks($contact, $request);
        });

        return redirect()
            ->route('manage-contact-details.index')
            ->with('message', 'Contact Details created successfully.');
    }

    public function edit($id)
    {
        $contact = ContactDetails::whereNull('deleted_by')->findOrFail($id);
        $ribbons = $contact->ribbonItems()->whereNull('deleted_by')->get();
        $socials = $contact->socialLinks()->whereNull('deleted_by')->get();

        return view('backend.contact_details.edit', [
            'contact'   => $contact,
            'ribbons'   => $ribbons,
            'socials'   => $socials,
            'platforms' => ContactSocialLink::PLATFORMS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $contact = ContactDetails::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request, true)->validate();

        $bannerDir = public_path(self::BANNER_DIR);
        $ribbonDir = public_path(self::RIBBON_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($ribbonDir);

        DB::transaction(function () use ($request, $contact, $bannerDir, $ribbonDir) {
            $bannerImage = $contact->banner_image;
            if ($request->hasFile('banner_image')) {
                $this->deleteFile($bannerDir, $bannerImage);
                $bannerImage = $this->uploadFile($request, 'banner_image', $bannerDir, 'banner');
            }

            $contact->update([
                'banner_heading'   => $request->banner_heading,
                'banner_image'     => $bannerImage,
                'address'          => $request->address,
                'email'            => $request->email,
                'footer_email'     => $request->footer_email,
                'emergency_no'     => $request->emergency_no,
                'join_team_email'  => $request->join_team_email,
                'donate_info'      => $request->donate_info,
                'map_url'          => $request->map_url,
                'iframe_url'       => $request->iframe_url,
                'updated_by'       => Auth::id(),
                'updated_at'       => Carbon::now(),
            ]);

            $this->syncRibbonItems($contact, $request, $ribbonDir);
            $this->syncSocialLinks($contact, $request);
        });

        return redirect()
            ->route('manage-contact-details.index')
            ->with('message', 'Contact Details updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $contact = ContactDetails::whereNull('deleted_by')->findOrFail($id);

            DB::transaction(function () use ($contact) {
                $now    = Carbon::now();
                $userId = Auth::id();

                $contact->ribbonItems()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $contact->socialLinks()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $contact->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
            });

            return redirect()
                ->route('manage-contact-details.index')
                ->with('message', 'Contact Details deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request, bool $isUpdate)
    {
        $imageRule = ($isUpdate ? 'nullable' : 'required')
            .'|file|image|mimes:jpg,jpeg,png,webp|max:10240';

        return Validator::make($request->all(), [
            'banner_heading'   => 'required|string|max:500',
            'banner_image'     => $imageRule,
            'address'          => 'required|string',
            'email'            => 'required|email|max:255',
            'footer_email'     => 'nullable|email|max:255',
            'emergency_no'     => 'required|string|max:100',
            'join_team_email'  => 'nullable|email|max:255',
            'donate_info'      => 'nullable|string',
            'map_url'          => 'nullable|string|max:2048',
            'iframe_url'       => 'nullable|string',

            'ribbon'                => 'nullable|array',
            'ribbon.*.title'        => 'required_with:ribbon.*|string|max:255',
            'ribbon.*.value'        => 'nullable|string|max:500',
            'ribbon.*.icon'         => 'nullable|file|image|mimes:jpg,jpeg,png,webp,svg|max:2048',

            'social'                => 'nullable|array',
            'social.*.platform'     => 'required_with:social.*.url|string|in:'.implode(',', array_keys(ContactSocialLink::PLATFORMS)),
            'social.*.url'          => 'required_with:social.*.platform|string|max:2048',
        ], [
            'banner_heading.required' => 'Please enter a Banner Heading.',
            'address.required'        => 'Please enter an Address.',
            'email.required'          => 'Please enter a primary Email.',
            'emergency_no.required'   => 'Please enter an Emergency Number.',
            'ribbon.*.title.required_with'    => 'Each ribbon row needs a Title.',
            'social.*.platform.in'            => 'Choose a valid social platform.',
            'social.*.url.required_with'      => 'Each social row needs a URL.',
        ]);
    }

    private function ensureFolder(string $folder): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    private function uploadFile(Request $request, string $key, string $folder, string $suffix): ?string
    {
        if (!$request->hasFile($key)) {
            return null;
        }
        $file = $request->file($key);
        $name = time().'_'.uniqid().'_'.$suffix.'.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);
        return $name;
    }

    private function deleteFile(string $folder, ?string $name): void
    {
        if (!empty($name) && file_exists($folder.'/'.$name)) {
            @unlink($folder.'/'.$name);
        }
    }

    private function createRibbonItems(ContactDetails $contact, Request $request, string $folder): void
    {
        $rows = $request->input('ribbon', []);
        $sort = 0;

        foreach ($rows as $idx => $row) {
            if (empty($row['title'])) { continue; }

            $iconName = null;
            if ($request->hasFile("ribbon.$idx.icon")) {
                $file     = $request->file("ribbon.$idx.icon");
                $iconName = time().'_'.uniqid().'_ribbon.'.$file->getClientOriginalExtension();
                $file->move($folder, $iconName);
            }

            ContactRibbonItem::create([
                'contact_details_id' => $contact->id,
                'icon'               => $iconName,
                'title'              => $row['title'],
                'value'              => $row['value'] ?? null,
                'sort_order'         => $sort++,
                'created_by'         => Auth::id(),
                'created_at'         => Carbon::now(),
            ]);
        }
    }

    private function syncRibbonItems(ContactDetails $contact, Request $request, string $folder): void
    {
        $rows    = $request->input('ribbon', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $idx => $row) {
            if (empty($row['title'])) { continue; }

            $rowId = isset($row['id']) ? (int) $row['id'] : 0;
            $file  = $request->file("ribbon.$idx.icon");

            if ($rowId > 0) {
                $existing = ContactRibbonItem::where('contact_details_id', $contact->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);

                if (!$existing) { continue; }

                $iconName = $existing->icon;
                if ($file) {
                    $this->deleteFile($folder, $iconName);
                    $iconName = time().'_'.uniqid().'_ribbon.'.$file->getClientOriginalExtension();
                    $file->move($folder, $iconName);
                }

                $existing->update([
                    'icon'       => $iconName,
                    'title'      => $row['title'],
                    'value'      => $row['value'] ?? null,
                    'sort_order' => $sort++,
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $iconName = null;
                if ($file) {
                    $iconName = time().'_'.uniqid().'_ribbon.'.$file->getClientOriginalExtension();
                    $file->move($folder, $iconName);
                }

                $created = ContactRibbonItem::create([
                    'contact_details_id' => $contact->id,
                    'icon'               => $iconName,
                    'title'              => $row['title'],
                    'value'              => $row['value'] ?? null,
                    'sort_order'         => $sort++,
                    'created_by'         => $userId,
                    'created_at'         => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        $toDelete = ContactRibbonItem::where('contact_details_id', $contact->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->get();

        foreach ($toDelete as $orphan) {
            $this->deleteFile($folder, $orphan->icon);
            $orphan->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
        }
    }

    private function createSocialLinks(ContactDetails $contact, Request $request): void
    {
        $rows = $request->input('social', []);
        $sort = 0;

        foreach ($rows as $row) {
            if (empty($row['platform']) || empty($row['url'])) { continue; }

            ContactSocialLink::create([
                'contact_details_id' => $contact->id,
                'platform'           => $row['platform'],
                'url'                => $row['url'],
                'sort_order'         => $sort++,
                'created_by'         => Auth::id(),
                'created_at'         => Carbon::now(),
            ]);
        }
    }

    private function syncSocialLinks(ContactDetails $contact, Request $request): void
    {
        $rows    = $request->input('social', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $row) {
            if (empty($row['platform']) || empty($row['url'])) { continue; }

            $rowId = isset($row['id']) ? (int) $row['id'] : 0;
            $data  = [
                'platform'   => $row['platform'],
                'url'        => $row['url'],
                'sort_order' => $sort++,
            ];

            if ($rowId > 0) {
                $existing = ContactSocialLink::where('contact_details_id', $contact->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);

                if (!$existing) { continue; }

                $existing->update($data + [
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $created = ContactSocialLink::create($data + [
                    'contact_details_id' => $contact->id,
                    'created_by'         => $userId,
                    'created_at'         => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        ContactSocialLink::where('contact_details_id', $contact->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
    }
}
