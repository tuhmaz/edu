<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\RoleAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $users = User::with('roles')
      ->when($request->get('role'), function ($query) use ($request) {
        $query->whereHas('roles', function ($query) use ($request) {
          $query->where('name', $request->get('role'));
        });
      })
      ->when($request->get('search'), function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
          $query->where('name', 'like', '%' . $request->get('search') . '%')
            ->orWhere('email', 'like', '%' . $request->get('search') . '%');
        });
      })
      ->paginate(10);

    return response()->json([
      'users' => $users,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'role' => 'required'
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $user->assignRole($request->role);

    return response()->json([
      'message' => 'User created successfully.',
      'user' => $user,
    ], 201);
  }

  public function show(Request $request, User $user)
  {
    return response()->json([
      'user' => $user->load('roles', 'permissions'),
    ]);
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
      'phone' => 'nullable|string|max:15',
      'job_title' => 'nullable|string|max:100',
      'gender' => 'nullable|in:male,female',
      'country' => 'nullable|string|max:100',
      'facebook_username' => 'nullable|string|max:255',
      'bio' => 'nullable|string',
      'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
      'is_online' => 'boolean',
    ]);

    $user->update($request->only([
      'name',
      'email',
      'phone',
      'job_title',
      'gender',
      'country',
      'bio'
    ]));

    if ($request->filled('facebook_username')) {
      $user->social_links = 'https://facebook.com/' . $request->facebook_username;
    }

    if ($request->hasFile('profile_photo')) {
      if ($user->profile_photo_path) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }
      $path = $request->file('profile_photo')->store('profile-photos', 'public');
      $user->profile_photo_path = $path;
    }

    $user->save();

    return response()->json([
      'message' => 'User updated successfully.',
      'user' => $user,
    ]);
  }

  public function destroy(Request $request, User $user)
  {
    $user->delete();

    return response()->json([
      'message' => 'User deleted successfully.',
    ]);
  }

  public function permissions_roles(Request $request, User $user)
  {
    $roles = Role::all();
    $permissions = Permission::all();

    return response()->json([
      'user' => $user->load('roles', 'permissions'),
      'roles' => $roles,
      'permissions' => $permissions,
    ]);
  }

  public function updatePermissionsRoles(Request $request, User $user)
  {
    $request->validate([
      'roles' => 'sometimes|array',
      'permissions' => 'sometimes|array',
    ]);

    $user->syncRoles($request->roles ?? []);
    $user->syncPermissions($request->permissions ?? []);

    return response()->json([
      'message' => 'User roles and permissions updated successfully.',
      'user' => $user->load('roles', 'permissions'),
    ]);
  }
}
