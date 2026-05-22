<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HomeTeam;


class HomeTeamController extends Controller
{

    public function index()
    {
        $teams = HomeTeam::whereNull('deleted_by')->latest()->get();

        return view('backend.home.our_team.index', compact('teams'));
    }

    public function create()
    {
        return view('backend.home.our_team.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
            ],
            [
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',
            ]
        );

        $validator->validate();

        HomeTeam::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('home-team.index')
            ->with('message', 'Team added successfully.');
    }

    public function edit($id)
    {
        $team = HomeTeam::findOrFail($id);
        return view('backend.home.our_team.edit', compact('team'));
    }

    public function update(Request $request, $id)
    {
        $team = HomeTeam::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
            ],
            [
                'title.required'       => 'Please enter Title.',
                'description.required' => 'Please enter Description.',
            ]
        );

        $validator->validate();

        $team->update([
            'title'       => $request->title,
            'description' => $request->description,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect()
            ->route('home-team.index')
            ->with('message', 'Team updated successfully.');
    }

    public function destroy(string $id)
    {
        $data['deleted_by'] =  Auth::user()->id;
        $data['deleted_at'] =  Carbon::now();
        try {
            $industries = HomeTeam::findOrFail($id);
            $industries->update($data);

            return redirect()->route('home-team.index')->with('message', 'Details deleted successfully!');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something Went Wrong - ' . $ex->getMessage());
        }
    }
}
