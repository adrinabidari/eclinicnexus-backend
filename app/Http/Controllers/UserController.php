<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role_id', [1, 3])
            ->orderBy('created_at', 'desc')
            ->get();
        return $users;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required'],
        ]);

        if ($request->hasFile('image')) {
            // Store the file in the 'images' directory within the 'public' disk
            $path = $request->file('image')->store('images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'profile' => $path ?? null,
            'role_id' => $request->role_id, //role_id = 1 for admin, 2 for doctor, 3 for staff and 4 for patient
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
