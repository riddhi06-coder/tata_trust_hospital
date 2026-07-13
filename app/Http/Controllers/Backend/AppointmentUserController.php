<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppointmentUser;
use Illuminate\Http\Request;

class AppointmentUserController extends Controller
{
    /**
     * List every appointment client (people who verified a mobile number),
     * with a count of how many appointments each has booked.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $users = AppointmentUser::whereNull('deleted_by')
            ->withCount(['appointments'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('last_verified_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('backend.appointments.users.index', compact('users', 'search'));
    }

    /**
     * Full history for a single client — profile plus every appointment they
     * have ever booked, each with its current status.
     */
    public function show($id)
    {
        $user = AppointmentUser::whereNull('deleted_by')
            ->with([
                'appointments' => fn ($q) => $q->with('status'),
            ])
            ->findOrFail($id);

        return view('backend.appointments.users.show', compact('user'));
    }
}
