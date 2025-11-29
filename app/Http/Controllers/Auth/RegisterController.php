<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Security: Only allow registration from authorized email
        $authorizedEmail = 'orhun_y@hotmail.com';
        if ($request->email !== $authorizedEmail) {
            return back()->withErrors([
                'email' => 'Kayıt işlemi sadece yetkili email adresi ile yapılabilir.'
            ])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'nullable|in:admin,lab_technician,receptionist',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'receptionist',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
