<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JoinPage;
use App\Models\JoinPageInfo;
use App\Models\JoinPageCommonRow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JoinPageController extends Controller
{
    private const BANNER_DIR = 'home/join-us/banner';
    private const INFO_DIR   = 'home/join-us/info';
    private const EXTRA_DIR  = 'home/join-us/extra';

    public function index()
    {
        $joinPage = JoinPage::whereNull('deleted_by')->first();

        return view('backend.join_us.page.index', compact('joinPage'));
    }

    public function create()
    {
        $existing = JoinPage::whereNull('deleted_by')->first();
        if ($existing) {
            return redirect()
                ->route('manage-join-page.edit', $existing->id)
                ->with('message', 'A Join Us page already exists. Edit it below.');
        }

        return view('backend.join_us.page.create');
    }

    public function store(Request $request)
    {
        if (JoinPage::whereNull('deleted_by')->exists()) {
            return redirect()
                ->route('manage-join-page.index')
                ->with('error', 'A Join Us page already exists. Delete it first before creating a new one.');
        }

        $this->validatePayload($request, false)->validate();

        $bannerDir = public_path(self::BANNER_DIR);
        $infoDir   = public_path(self::INFO_DIR);
        $extraDir  = public_path(self::EXTRA_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($infoDir);
        $this->ensureFolder($extraDir);

        DB::transaction(function () use ($request, $bannerDir, $infoDir, $extraDir) {
            $bannerImage = $this->uploadFile($request, 'banner_image', $bannerDir, 'banner');
            $extraImage  = $this->uploadFile($request, 'extra_background_image', $extraDir, 'extra');

            $joinPage = JoinPage::create([
                'banner_heading'          => $request->banner_heading,
                'banner_image'            => $bannerImage,
                'section_heading'         => $request->section_heading,
                'section_description'     => $request->section_description,
                'current_job_title'       => $request->current_job_title,
                'current_job_description' => $request->current_job_description,
                'common_heading'          => $request->common_heading,
                'common_title'            => $request->common_title,
                'common_description'      => $request->common_description,
                'extra_background_image'  => $extraImage,
                'extra_description'       => $request->extra_description,
                'created_by'              => Auth::id(),
                'created_at'              => Carbon::now(),
            ]);

            $this->createInfoRows($joinPage, $request, $infoDir);
            $this->createCommonRows($joinPage, $request);
        });

        return redirect()
            ->route('manage-join-page.index')
            ->with('message', 'Join Us page created successfully.');
    }

    public function edit($id)
    {
        $joinPage = JoinPage::whereNull('deleted_by')->findOrFail($id);
        $infos    = $joinPage->infos()->whereNull('deleted_by')->get();
        $commons  = $joinPage->commonRows()->whereNull('deleted_by')->get();

        return view('backend.join_us.page.edit', compact('joinPage', 'infos', 'commons'));
    }

    public function update(Request $request, $id)
    {
        $joinPage = JoinPage::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request, true)->validate();

        $bannerDir = public_path(self::BANNER_DIR);
        $infoDir   = public_path(self::INFO_DIR);
        $extraDir  = public_path(self::EXTRA_DIR);
        $this->ensureFolder($bannerDir);
        $this->ensureFolder($infoDir);
        $this->ensureFolder($extraDir);

        DB::transaction(function () use ($request, $joinPage, $bannerDir, $infoDir, $extraDir) {

            $bannerImage = $joinPage->banner_image;
            if ($request->hasFile('banner_image')) {
                $this->deleteFile($bannerDir, $bannerImage);
                $bannerImage = $this->uploadFile($request, 'banner_image', $bannerDir, 'banner');
            }

            $extraImage = $joinPage->extra_background_image;
            if ($request->hasFile('extra_background_image')) {
                $this->deleteFile($extraDir, $extraImage);
                $extraImage = $this->uploadFile($request, 'extra_background_image', $extraDir, 'extra');
            }

            $joinPage->update([
                'banner_heading'          => $request->banner_heading,
                'banner_image'            => $bannerImage,
                'section_heading'         => $request->section_heading,
                'section_description'     => $request->section_description,
                'current_job_title'       => $request->current_job_title,
                'current_job_description' => $request->current_job_description,
                'common_heading'          => $request->common_heading,
                'common_title'            => $request->common_title,
                'common_description'      => $request->common_description,
                'extra_background_image'  => $extraImage,
                'extra_description'       => $request->extra_description,
                'updated_by'              => Auth::id(),
                'updated_at'              => Carbon::now(),
            ]);

            $this->syncInfoRows($joinPage, $request, $infoDir);
            $this->syncCommonRows($joinPage, $request);
        });

        return redirect()
            ->route('manage-join-page.index')
            ->with('message', 'Join Us page updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $joinPage = JoinPage::whereNull('deleted_by')->findOrFail($id);

            DB::transaction(function () use ($joinPage) {
                $now    = Carbon::now();
                $userId = Auth::id();

                $joinPage->infos()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $joinPage->commonRows()->whereNull('deleted_by')->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
                $joinPage->update([
                    'deleted_by' => $userId,
                    'deleted_at' => $now,
                ]);
            });

            return redirect()
                ->route('manage-join-page.index')
                ->with('message', 'Join Us page deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* ---------------------------------------------------------------------
     |  Helpers
     |---------------------------------------------------------------------*/

    private function validatePayload(Request $request, bool $isUpdate)
    {
        $topImageRule = ($isUpdate ? 'nullable' : 'required').'|file|image|mimes:jpg,jpeg,png,webp|max:10240';

        $rules = [
            'banner_heading'          => 'required|string|max:255',
            'banner_image'            => $topImageRule,
            'section_heading'         => 'required|string|max:255',
            'section_description'     => 'nullable|string',
            'current_job_title'       => 'required|string|max:255',
            'current_job_description' => 'required|string',
            'common_heading'          => 'required|string|max:255',
            'common_title'            => 'required|string|max:255',
            'common_description'      => 'required|string',
            'extra_background_image'  => $topImageRule,
            'extra_description'       => 'required|string',

            'info'               => 'required|array|min:1',
            'info.*.title'       => 'required|string|max:255',
            'info.*.description' => 'required|string',
            // On update, an existing row can keep its image without re-uploading.
            'info.*.image'       => ($isUpdate ? 'nullable' : 'required').'|file|image|mimes:jpg,jpeg,png,webp,svg|max:5120',

            'common_rows'                => 'required|array|min:1',
            'common_rows.*.job_title'    => 'required|string|max:255',
            'common_rows.*.subject'      => 'required|string|max:255',
            'common_rows.*.description'  => 'required|string',
        ];

        return Validator::make($request->all(), $rules, [
            'info.required'        => 'Add at least one Info row.',
            'common_rows.required' => 'Add at least one Common Section row.',
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

    private function createInfoRows(JoinPage $joinPage, Request $request, string $folder): void
    {
        $rows = $request->input('info', []);
        $sort = 0;

        foreach ($rows as $idx => $row) {
            $imageName = null;
            if ($request->hasFile("info.$idx.image")) {
                $file      = $request->file("info.$idx.image");
                $imageName = time().'_'.uniqid().'_info.'.$file->getClientOriginalExtension();
                $file->move($folder, $imageName);
            }

            JoinPageInfo::create([
                'join_page_id' => $joinPage->id,
                'image'        => $imageName,
                'title'        => $row['title'] ?? '',
                'description'  => $row['description'] ?? '',
                'sort_order'   => $sort++,
                'created_by'   => Auth::id(),
                'created_at'   => Carbon::now(),
            ]);
        }
    }

    private function createCommonRows(JoinPage $joinPage, Request $request): void
    {
        $rows = $request->input('common_rows', []);
        $sort = 0;

        foreach ($rows as $row) {
            JoinPageCommonRow::create([
                'join_page_id' => $joinPage->id,
                'job_title'    => $row['job_title'] ?? '',
                'subject'      => $row['subject'] ?? '',
                'description'  => $row['description'] ?? '',
                'sort_order'   => $sort++,
                'created_by'   => Auth::id(),
                'created_at'   => Carbon::now(),
            ]);
        }
    }

    private function syncInfoRows(JoinPage $joinPage, Request $request, string $folder): void
    {
        $rows    = $request->input('info', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $idx => $row) {
            $rowId = isset($row['id']) ? (int) $row['id'] : 0;
            $file  = $request->file("info.$idx.image");

            if ($rowId > 0) {
                $existing = JoinPageInfo::where('join_page_id', $joinPage->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);

                if (!$existing) { continue; }

                $imageName = $existing->image;
                if ($file) {
                    $this->deleteFile($folder, $imageName);
                    $imageName = time().'_'.uniqid().'_info.'.$file->getClientOriginalExtension();
                    $file->move($folder, $imageName);
                }

                $existing->update([
                    'image'       => $imageName,
                    'title'       => $row['title'] ?? '',
                    'description' => $row['description'] ?? '',
                    'sort_order'  => $sort++,
                    'updated_by'  => $userId,
                    'updated_at'  => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $imageName = null;
                if ($file) {
                    $imageName = time().'_'.uniqid().'_info.'.$file->getClientOriginalExtension();
                    $file->move($folder, $imageName);
                }

                $created = JoinPageInfo::create([
                    'join_page_id' => $joinPage->id,
                    'image'        => $imageName,
                    'title'        => $row['title'] ?? '',
                    'description'  => $row['description'] ?? '',
                    'sort_order'   => $sort++,
                    'created_by'   => $userId,
                    'created_at'   => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        $toDelete = JoinPageInfo::where('join_page_id', $joinPage->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->get();

        foreach ($toDelete as $orphan) {
            $this->deleteFile($folder, $orphan->image);
            $orphan->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
        }
    }

    private function syncCommonRows(JoinPage $joinPage, Request $request): void
    {
        $rows    = $request->input('common_rows', []);
        $keepIds = [];
        $sort    = 0;
        $now     = Carbon::now();
        $userId  = Auth::id();

        foreach ($rows as $row) {
            $rowId = isset($row['id']) ? (int) $row['id'] : 0;

            $data = [
                'job_title'   => $row['job_title'] ?? '',
                'subject'     => $row['subject'] ?? '',
                'description' => $row['description'] ?? '',
                'sort_order'  => $sort++,
            ];

            if ($rowId > 0) {
                $existing = JoinPageCommonRow::where('join_page_id', $joinPage->id)
                    ->whereNull('deleted_by')
                    ->find($rowId);

                if (!$existing) { continue; }

                $existing->update($data + [
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $keepIds[] = $existing->id;
            } else {
                $created = JoinPageCommonRow::create($data + [
                    'join_page_id' => $joinPage->id,
                    'created_by'   => $userId,
                    'created_at'   => $now,
                ]);
                $keepIds[] = $created->id;
            }
        }

        JoinPageCommonRow::where('join_page_id', $joinPage->id)
            ->whereNull('deleted_by')
            ->whereNotIn('id', $keepIds)
            ->update([
                'deleted_by' => $userId,
                'deleted_at' => $now,
            ]);
    }
}
