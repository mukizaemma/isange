<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::query()->orderBy('name')->get();

        return view('admin.users', compact('users'));
    }

    public function verify(User $user)
    {
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users')->with('success', $user->name.' has been marked as verified.');
    }

    public function unverify(User $user)
    {
        $user->email_verified_at = null;
        $user->save();

        return redirect()->route('admin.users')->with('success', $user->name.' verification has been removed.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in([User::ROLE_STAFF, User::ROLE_ADMIN])],
        ]);

        if ($user->isSuperAdmin() && $request->input('role') !== User::ROLE_ADMIN) {
            return redirect()->route('admin.users')->with('error', 'The primary super admin account must remain an admin.');
        }

        $user->role = $request->input('role');
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Role updated for '.$user->name.'.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Password updated for '.$user->name.'.');
    }
}
