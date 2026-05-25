<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeBoard;


class HomeBoardController extends Controller
{

    public function index()
    {
        $boards = HomeBoard::whereNull('deleted_by')->latest()->get();

        return view('backend.home.board.index', compact('boards'));
    }

    public function create()
    {
        return view('backend.home.board.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'image'       => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required'       => 'Please enter Heading.',
                'title.max'            => 'Heading must be 255 characters or less.',

                'description.required' => 'Please enter Description.',

                'image.required' => 'Please upload an image.',
                'image.image'    => 'File must be an image.',
                'image.mimes'    => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'      => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder = public_path('home/board');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file     = $request->file('image');
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($folder, $fileName);

        HomeBoard::create([
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $fileName,
            'created_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-board.index')
            ->with('message', 'Board entry added successfully.');
    }

    public function edit($id)
    {
        $board = HomeBoard::findOrFail($id);
        return view('backend.home.board.edit', compact('board'));
    }

    public function update(Request $request, $id)
    {
        $board = HomeBoard::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'image'       => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'title.required'       => 'Please enter Heading.',
                'title.max'            => 'Heading must be 255 characters or less.',

                'description.required' => 'Please enter Description.',

                'image.image' => 'File must be an image.',
                'image.mimes' => 'Allowed image formats: jpg, jpeg, png, webp.',
                'image.max'   => 'Image must be 2MB or smaller.',
            ]
        );

        $validator->validate();

        $folder   = public_path('home/board');
        $fileName = $board->image;

        if ($request->hasFile('image')) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            if (!empty($board->image) && file_exists($folder.'/'.$board->image)) {
                @unlink($folder.'/'.$board->image);
            }

            $file     = $request->file('image');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($folder, $fileName);
        }

        $board->update([
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $fileName,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('manage-board.index')
            ->with('message', 'Board entry updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $board = HomeBoard::findOrFail($id);
            $board->update($data);

            return redirect()->route('manage-board.index')->with('message', 'Details deleted successfully!');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
