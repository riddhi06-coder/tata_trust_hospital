<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobRoleController extends Controller
{
    private const JD_DIR = 'home/join-us/jd';

    public function index()
    {
        $postings = JobRole::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('backend.join_us.job_postings.index', compact('postings'));
    }

    public function create()
    {
        return view('backend.join_us.job_postings.create', [
            'jobTypes'  => JobRole::JOB_TYPES,
            'workModes' => JobRole::WORK_MODES,
        ]);
    }

    public function store(Request $request)
    {
        $this->validatePayload($request, false)->validate();

        $folder = public_path(self::JD_DIR);
        $this->ensureFolder($folder);

        $jdFile = $this->uploadJd($request, $folder);

        JobRole::create([
            'job_position' => $request->job_position,
            'slug'         => $this->uniqueSlug($request->job_position),
            'job_location' => $request->job_location,
            'job_type'     => $request->job_type,
            'work_mode'    => $request->work_mode,
            'jd_file'      => $jdFile,
            'created_by'   => Auth::id(),
            'created_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-job-role.index')
            ->with('message', 'Job posting added successfully.');
    }

    public function edit($id)
    {
        $posting = JobRole::whereNull('deleted_by')->findOrFail($id);

        return view('backend.join_us.job_postings.edit', [
            'posting'   => $posting,
            'jobTypes'  => JobRole::JOB_TYPES,
            'workModes' => JobRole::WORK_MODES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $posting = JobRole::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request, true)->validate();

        $folder = public_path(self::JD_DIR);
        $this->ensureFolder($folder);

        $jdFile = $posting->jd_file;
        if ($request->hasFile('jd_file')) {
            $this->deleteFile($folder, $jdFile);
            $jdFile = $this->uploadJd($request, $folder);
        }

        $slug = $posting->slug;
        if ($request->job_position !== $posting->job_position || empty($slug)) {
            $slug = $this->uniqueSlug($request->job_position, $posting->id);
        }

        $posting->update([
            'job_position' => $request->job_position,
            'slug'         => $slug,
            'job_location' => $request->job_location,
            'job_type'     => $request->job_type,
            'work_mode'    => $request->work_mode,
            'jd_file'      => $jdFile,
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-job-role.index')
            ->with('message', 'Job posting updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $posting = JobRole::whereNull('deleted_by')->findOrFail($id);
            $posting->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-job-role.index')
                ->with('message', 'Job posting deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request, bool $isUpdate)
    {
        $jdRule = ($isUpdate ? 'nullable' : 'required')
            .'|file|mimes:pdf,doc,docx|max:5120';

        return Validator::make($request->all(), [
            'job_position' => 'required|string|max:255',
            'job_location' => 'nullable|string|max:255',
            'job_type'     => 'required|string|max:100',
            'work_mode'    => 'required|string|max:100',
            'jd_file'      => $jdRule,
        ], [
            'job_position.required' => 'Please enter a Job Position.',
            'job_type.required'     => 'Please select a Job Type.',
            'work_mode.required'    => 'Please select a Work Mode.',
            'jd_file.mimes'         => 'JD must be a PDF or Word document (.pdf, .doc, .docx).',
            'jd_file.max'           => 'JD must be 5MB or smaller.',
        ]);
    }

    private function ensureFolder(string $folder): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    private function uploadJd(Request $request, string $folder): ?string
    {
        if (!$request->hasFile('jd_file')) {
            return null;
        }
        $file = $request->file('jd_file');
        $name = time().'_'.uniqid().'_jd.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);
        return $name;
    }

    private function deleteFile(string $folder, ?string $name): void
    {
        if (!empty($name) && file_exists($folder.'/'.$name)) {
            @unlink($folder.'/'.$name);
        }
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'job-'.uniqid();
        }

        $slug = $base;
        $i    = 1;
        while (
            JobRole::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
