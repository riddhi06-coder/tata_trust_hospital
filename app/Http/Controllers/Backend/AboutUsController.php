<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AboutUs;

class AboutUsController extends Controller
{
    /** Directory (under /public) for all About Us images. */
    private string $folder = 'about-us';

    public function index()
    {
        $about = AboutUs::whereNull('deleted_by')->first();

        return view('backend.about_us.index', compact('about'));
    }

    public function create()
    {
        // About Us is a single page — if it already exists, edit it instead.
        $about = AboutUs::whereNull('deleted_by')->first();
        if ($about) {
            return redirect()->route('manage-about-us.edit', $about->id);
        }

        return view('backend.about_us.create');
    }

    public function store(Request $request)
    {
        $this->validateData($request);
        $this->persist($request, null);

        return redirect()
            ->route('manage-about-us.index')
            ->with('message', 'About Us saved successfully.');
    }

    public function edit($id)
    {
        $about = AboutUs::findOrFail($id);

        return view('backend.about_us.edit', compact('about'));
    }

    public function update(Request $request, $id)
    {
        $about = AboutUs::findOrFail($id);

        $this->validateData($request);
        $this->persist($request, $about);

        return redirect()
            ->route('manage-about-us.index')
            ->with('message', 'About Us updated successfully.');
    }

    public function destroy(string $id)
    {
        try {
            $about = AboutUs::findOrFail($id);
            $about->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => Carbon::now(),
            ]);

            return redirect()->route('manage-about-us.index')->with('message', 'About Us deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong - '.$ex->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function validateData(Request $request): void
    {
        Validator::make($request->all(), [
            'banner_heading'       => 'required|string|max:255',
            'banner_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'about_heading'        => 'required|string|max:255',
            'about_description'    => 'required|string',
            'about_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'about_info_image.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'about_info_heading.*' => 'nullable|string|max:255',
            'about_info_desc.*'    => 'nullable|string',

            'values_heading'       => 'required|string|max:255',
            'values_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'values_description'   => 'required|string',

            'commitment_heading'   => 'required|string|max:255',
            'commitment_image.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'commitment_count.*'   => 'nullable|string|max:255',
            'commitment_title.*'   => 'nullable|string|max:255',

            'contact_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'contact_description'  => 'required|string',
        ], [
            'banner_heading.required'    => 'Please enter the Banner Heading.',
            'about_heading.required'     => 'Please enter the About Heading.',
            'about_description.required' => 'Please enter the About Description.',
            'values_heading.required'    => 'Please enter the Our Values Heading.',
            'values_description.required'=> 'Please enter the Our Values Description.',
            'commitment_heading.required'=> 'Please enter the Reflecting Commitment Heading.',
            'contact_description.required'=> 'Please enter the Contact Description.',
            'banner_image.max'       => 'Banner image must be 2MB or smaller.',
            'about_image.max'        => 'About image must be 2MB or smaller.',
            'about_info_image.*.max' => 'Each info image must be 2MB or smaller.',
            'values_image.max'       => 'Values image must be 2MB or smaller.',
            'commitment_image.*.max' => 'Each commitment image must be 2MB or smaller.',
            'contact_image.max'      => 'Contact image must be 2MB or smaller.',
            'banner_image.image'     => 'Banner file must be an image.',
            'about_image.image'      => 'About file must be an image.',
            'values_image.image'     => 'Values file must be an image.',
            'contact_image.image'    => 'Contact file must be an image.',
        ])->validate();
    }

    private function persist(Request $request, ?AboutUs $about): void
    {
        $folder = public_path($this->folder);
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $data = [
            'banner_heading'      => $request->banner_heading,
            'banner_image'        => $this->handleSingleImage($request, 'banner_image', $folder, $about->banner_image ?? null, '_banner'),

            'about_heading'       => $request->about_heading,
            'about_description'   => $request->about_description,
            'about_image'         => $this->handleSingleImage($request, 'about_image', $folder, $about->about_image ?? null, '_about'),
            'about_info_items'    => $this->buildInfoItems($request, $folder, $about->about_info_items ?? []),

            'values_heading'      => $request->values_heading,
            'values_image'        => $this->handleSingleImage($request, 'values_image', $folder, $about->values_image ?? null, '_values'),
            'values_description'  => $request->values_description,

            'commitment_heading'  => $request->commitment_heading,
            'commitment_items'    => $this->buildCommitmentItems($request, $folder, $about->commitment_items ?? []),

            'contact_image'       => $this->handleSingleImage($request, 'contact_image', $folder, $about->contact_image ?? null, '_contact'),
            'contact_description' => $request->contact_description,
        ];

        if ($about) {
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = Carbon::now();
            $about->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $data['created_at'] = Carbon::now();
            AboutUs::create($data);
        }
    }

    /** About-section info cards: image + heading + rich description. */
    private function buildInfoItems(Request $request, string $folder, array $oldItems): array
    {
        $headings = $request->input('about_info_heading', []);
        $descs    = $request->input('about_info_desc', []);
        $existing = $request->input('about_info_existing', []);
        $files    = $request->file('about_info_image', []);

        $items = [];
        foreach ($headings as $i => $heading) {
            $desc    = $descs[$i] ?? null;
            $newFile = $files[$i] ?? null;
            $img     = $existing[$i] ?? null;

            // Skip a row that has nothing in it.
            $hasContent = filled($heading) || filled(trim(strip_tags((string) $desc))) || $newFile || filled($img);
            if (! $hasContent) {
                continue;
            }

            if ($newFile) {
                if (filled($img) && file_exists($folder.'/'.$img)) {
                    @unlink($folder.'/'.$img);
                }
                $img = $this->moveImage($newFile, $folder, '_info');
            }

            $items[] = [
                'image'       => $img,
                'heading'     => $heading,
                'description' => $desc,
            ];
        }

        $this->cleanupOrphans($folder, array_column($oldItems, 'image'), array_column($items, 'image'));

        return $items;
    }

    /** Reflecting-commitment cards: image + count + title. */
    private function buildCommitmentItems(Request $request, string $folder, array $oldItems): array
    {
        $counts   = $request->input('commitment_count', []);
        $titles   = $request->input('commitment_title', []);
        $existing = $request->input('commitment_existing', []);
        $files    = $request->file('commitment_image', []);

        // Iterate over the union of title/count keys so a row with only a count still counts.
        $keys = array_unique(array_merge(array_keys($titles), array_keys($counts), array_keys($existing)));

        $items = [];
        foreach ($keys as $i) {
            $count   = $counts[$i] ?? null;
            $title   = $titles[$i] ?? null;
            $newFile = $files[$i] ?? null;
            $img     = $existing[$i] ?? null;

            $hasContent = filled($count) || filled($title) || $newFile || filled($img);
            if (! $hasContent) {
                continue;
            }

            if ($newFile) {
                if (filled($img) && file_exists($folder.'/'.$img)) {
                    @unlink($folder.'/'.$img);
                }
                $img = $this->moveImage($newFile, $folder, '_commit');
            }

            $items[] = [
                'image' => $img,
                'count' => $count,
                'title' => $title,
            ];
        }

        $this->cleanupOrphans($folder, array_column($oldItems, 'image'), array_column($items, 'image'));

        return $items;
    }

    private function handleSingleImage(Request $request, string $field, string $folder, ?string $current, string $suffix): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }
        if (filled($current) && file_exists($folder.'/'.$current)) {
            @unlink($folder.'/'.$current);
        }

        return $this->moveImage($request->file($field), $folder, $suffix);
    }

    private function moveImage($file, string $folder, string $suffix = ''): string
    {
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        $name = time().'_'.uniqid().$suffix.'.'.$file->getClientOriginalExtension();
        $file->move($folder, $name);

        return $name;
    }

    private function cleanupOrphans(string $folder, array $old, array $new): void
    {
        $old = array_filter($old);
        $new = array_filter($new);
        foreach (array_diff($old, $new) as $orphan) {
            if (filled($orphan) && file_exists($folder.'/'.$orphan)) {
                @unlink($folder.'/'.$orphan);
            }
        }
    }
}
