<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Flyer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FlyerController extends Controller
{
    private const FILE_DIR = 'home/flyer';

    public function index()
    {
        $flyers = Flyer::whereNull('deleted_by')
            ->orderByDesc('id')
            ->get();

        return view('backend.flyer.index', compact('flyers'));
    }

    public function create()
    {
        return view('backend.flyer.create');
    }

    public function store(Request $request)
    {
        $this->validatePayload($request)->validate();

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        Flyer::create([
            'flyer_image' => $this->uploadFile($request, $folder),
            'is_active'   => true, // new flyers are active by default; toggle from the list
            'created_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-flyer.index')
            ->with('message', 'Flyer added successfully.');
    }

    public function edit($id)
    {
        $flyer = Flyer::whereNull('deleted_by')->findOrFail($id);

        return view('backend.flyer.edit', compact('flyer'));
    }

    public function update(Request $request, $id)
    {
        $flyer = Flyer::whereNull('deleted_by')->findOrFail($id);

        $this->validatePayload($request)->validate();

        $folder = public_path(self::FILE_DIR);
        $this->ensureFolder($folder);

        $flyerImage = $flyer->flyer_image;
        if ($request->hasFile('flyer_image')) {
            $this->deleteFile($folder, $flyerImage);
            $flyerImage = $this->uploadFile($request, $folder);
        }

        $flyer->update([
            'flyer_image' => $flyerImage,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-flyer.index')
            ->with('message', 'Flyer updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $flyer = Flyer::whereNull('deleted_by')->findOrFail($id);

            $flyer->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('manage-flyer.index')
                ->with('message', 'Flyer deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $flyer = Flyer::whereNull('deleted_by')->findOrFail($id);
            $flyer->is_active  = ! $flyer->is_active;
            $flyer->updated_by = Auth::id();
            $flyer->save();

            return response()->json([
                'success'   => true,
                'is_active' => $flyer->is_active,
                'message'   => $flyer->is_active ? 'Flyer is now active.' : 'Flyer is now inactive.',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong - '.$ex->getMessage(),
            ], 500);
        }
    }

    /* --------------------------------------------------------------------- */

    private function validatePayload(Request $request)
    {
        return Validator::make($request->all(), [
            'flyer_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'flyer_image.image' => 'Flyer image must be an image (jpg, jpeg, png, webp).',
            'flyer_image.mimes' => 'Only jpg, jpeg, png and webp files are allowed.',
            'flyer_image.max'   => 'Flyer image must be 10MB or smaller.',
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
        if (!$request->hasFile('flyer_image')) {
            return null;
        }
        $file = $request->file('flyer_image');
        $name = time().'_'.uniqid().'_flyer.'.$file->getClientOriginalExtension();
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
