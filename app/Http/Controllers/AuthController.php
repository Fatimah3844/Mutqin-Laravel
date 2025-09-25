<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class AuthController extends Controller
{
    // ================== Signup (email/password) ==================
    public function signup(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|string|in:student,sheikh,parent,admin',
        ]);

        $user = User::create([
            'username' => $request->username,
            'name'     => $request->name,
            'phone'    => $request->phone, 
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'User registered successfully.',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    // ================== Login (email/password) ==================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // ================== Logout ==================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // ================== Profile ==================
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // ================== Google OAuth Redirect ==================
    public function redirectToGoogle()
    {
        /** @var GoogleProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->stateless()->redirect();
    }

    // ================== Google OAuth Callback ==================
    public function handleGoogleCallback()
{
     /** @var GoogleProvider $driver */
        $driver = Socialite::driver('google');
        $googleUser = $driver->stateless()->user();

    // default role
    $defaultRole = 'student'; 

    // check if user have same email first
    $user = User::where('email', $googleUser->email)->first();

    if ($user) {
        
        $role = $user->role;
    } else {
        
        $user = User::create([
             'name'       => $googleUser->name ?? 'No Name',
    'username'   => $googleUser->name ?? 'user' . rand(1000,9999), 
    'email'      => $googleUser->email,
    'google_id'  => $googleUser->id,
    'password'   => Hash::make(uniqid()),
    'role'       => $defaultRole,
        ]);
        $role = $defaultRole;
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'role' => $role, // from database
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
}

}
