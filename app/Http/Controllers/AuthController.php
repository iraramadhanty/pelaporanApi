<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) return response()->json([
            'message' => 'Login Failed'
        ], 401);
        $user = User::where("username", $request->username)->first();
        return response()->json([
            'message' => 'Login Success',
            'user' => $user,
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(Request $request)
    {
        $attr = Validator::make($request->all(), [
            'nik' => ['required', 'size:16'],
            'name' => ['required', 'min:3'],
            'username' => ['required', 'min:6', 'max:12', Rule::unique('users')],
            'password' => ['required', 'min:8', 'max:16', 'confirmed'],
        ]);

        if ($attr->fails()) return response()->json([
            'message' => 'Your Request Invalid',
            'errors'  => $attr->errors()
        ]);
        $user = User::create([
            'role' => 'masyarakat',
            'username' => $request->username,
            'name' => $request->name,
            'nik' => $request->nik,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Register Success',
            'user' => $user,
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }
}
