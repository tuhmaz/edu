<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class AuthController extends Controller
{
  /**
   * Register a new user.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    // Validate the input
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|confirmed|min:8',
    ]);

    // Create the user
    $user = User::create([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
    ]);

    // Automatically log in the user
    Auth::login($user);

    return redirect('/')->with('message', 'Registration successful. Welcome!');
  }

  /**
   * Log in the user and return a token.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
      // Validate the input
      $validated = $request->validate([
          'email' => 'required|string|email',
          'password' => 'required|string|min:8',
      ]);

      // Attempt to authenticate the user
      if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
          if ($request->expectsJson()) {
              return response()->json([
                  'status' => false,
                  'message' => 'Invalid credentials.',
              ], 401);
          }

          return redirect()->back()->withErrors([
              'email' => 'Invalid credentials.',
          ]);
      }

      // Retrieve authenticated user
      $user = Auth::user();

      // Generate a token using Sanctum
      $token = $user->createToken('AccessToken')->plainTextToken;

      // Check if the request expects JSON (API) or web response
      
      // Redirect to the homepage with a success message and set a cookie for web requests
      return redirect('/')
          ->with('message', 'Login successful.')
          ->cookie('access_token', $token, 60 * 24); // Store the token in a cookie
  }


  /**
   * Get the authenticated user's profile.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function profile()
  {
    $user = auth()->user();

    if (!$user) {
      return response()->json([
        'status' => false,
        'message' => 'User not authenticated.',
      ], 401);
    }

    return response()->json([
      'status' => true,
      'message' => 'Profile information retrieved successfully.',
      'data' => $user,
    ], 200);
  }

  /**
   * Log out the user and revoke the token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    $user = auth()->user();

    if ($user && $user->currentAccessToken()) {
      $user->currentAccessToken()->delete();
    }

    return response()->json([
      'status' => true,
      'message' => 'User logged out successfully.',
    ], 200);
  }
}
