<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST [ name, email, password ]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user); // تسجيل الدخول بعد التسجيل

        return response()->json([
          'status' => true,
          'message' => 'User created successfully',
          'data' => $user,
      ], 201); // يجب أن يكون 201
    }

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('myAccessToken')->plainTextToken;

    return response()->json([
      'status' => true,
      'message' => 'Login successful',
      'token' => $token,
      'user' => $user,
  ], 200); // يجب أن يكون 200


}


    public function profile()
    {
        $userData = auth()->user(); // تأكد من أن هذا الاستدعاء يتم بشكل صحيح

        return response()->json([
            "status" => true,
            "message" => "Profile information",
            "data" => $userData,
            "id" => auth()->user()->id // ربما تسبب في الخطأ إذا كان `auth()` لم يكن معرفًا بشكل صحيح
        ]);
    }


    public function logout()
    {
        $token = auth()->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            "status" => true,
            "message" => "User Logged out successfully"
        ]);
    }
}
