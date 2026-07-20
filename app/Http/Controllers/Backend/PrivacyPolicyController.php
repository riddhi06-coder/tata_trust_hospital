<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrivacyPolicyController extends Controller
{
    private const FILE_DIR = 'home/privacy';

    public function index()
    {
        $policies = PrivacyPolicy::whereNull('deleted_by')
            ->orderBy('name')
            ->get();

        return view('backend.privacy.index', compact('policies'));
    }

    public function create()
    {
        return view('backend.privacy.create');
    }

    public function store(Request $request)
    {
        $this->validatePayload($request, false)->validate();

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        $file = $this->uploadFile($request, $folder);

        PrivacyPolicy::create([
            'name'       => $request->input('name'),
            'file'       => $file,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-privacy-policy.index')
            ->with('message', 'Policy uploaded successfully.');
    }

    public function edit($id)
    {
        $policy = PrivacyPolicy::whereNull('deleted_by')->findOrFail($id);

        return view('backend.privacy.edit', compact('policy'));
    }

    public function update(Request $request, $id)
    {
        $policy = PrivacyPolicy::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request, true)->validate();

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        $file = $policy->file;
        if ($request->hasFile('file')) {
            $this->deleteFile($folder, $file);
            $file = $this->uploadFile($request, $folder);
        }

        $policy->update([
            'name'       => $request->input('name'),
            'file'       => $file,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-privacy-policy.index')
            ->with('message', 'Policy updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $policy = PrivacyPolicy::whereNull('deleted_by')->findOrFail($id);

            $policy->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-privacy-policy.index')
                ->with('message', 'Policy deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request, bool $isUpdate)
    {
        $rule = ($isUpdate ? 'nullable' : 'required')
            .'|file|mimes:pdf,doc,docx|max:5120';

        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => $rule,
        ], [
            'name.required' => 'Please enter a policy name.',
            'name.max'      => 'Policy name should not exceed 255 characters.',
            'file.required' => 'Please upload a policy document.',
            'file.mimes'    => 'Policy must be a PDF or Word document (.pdf, .doc, .docx).',
            'file.max'      => 'Policy must be 5MB or smaller.',
        ]);
    }

    private function ensureFolder(string $folder): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    private function uploadFile(Request $request, string $folder): ?string
    {
        if (!$request->hasFile('file')) {
            return null;
        }
        $file = $request->file('file');
        $name = time().'_'.uniqid().'_privacy.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);
        return $name;
    }

    private function deleteFile(string $folder, ?string $name): void
    {
        if (!empty($name) && file_exists($folder.'/'.$name)) {
            @unlink($folder.'/'.$name);
        }
    }
}
