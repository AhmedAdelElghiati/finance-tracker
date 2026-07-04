<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required' , 'string' , 'email' , 'max:255'],
            'password' => ['required' , 'string' , Password::defaults() ]
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ] , 201);
    }

    public function login(Request $request) {
        $validatedData = $request->validate([
            'email' => ['required' , 'string' , 'email' , 'max:255'],
            'password' => ['required' , 'string' , Password::defaults() ]
        ]);

        $user = User::where('email' , $validatedData['email'])->first();
        if (!$user || !Hash::check($validatedData['password'] , $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.'
            ] , 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ] ,200);
    }
}
