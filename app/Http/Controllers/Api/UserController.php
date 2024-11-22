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

    $usersData = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'gender' => $user->gender,
            'country' => $user->country,
            'status' => $user->status,
            'last_activity' => $user->last_activity,
            'profile_photo_url' => $user->profile_photo_url,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];
    });

    return response()->json([
        'status' => true,
        'message' => 'Users retrieved successfully.',
        'data' => $usersData,
        'pagination' => [
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'first_page_url' => $users->url(1),
            'last_page_url' => $users->url($users->lastPage()),
            'next_page_url' => $users->nextPageUrl(),
            'prev_page_url' => $users->previousPageUrl(),
        ]
    ], 200);
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

  public function show($id)
  {
    $user = User::with(['roles', 'permissions'])->find($id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found.',
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'User details retrieved successfully.',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'gender' => $user->gender,
            'country' => $user->country,
            'social_links' => $user->social_links,
            'bio' => $user->bio,
            'status' => $user->status,
            'last_activity' => $user->last_activity,
            'profile_photo_url' => $user->profile_photo_url,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
            'roles' => $user->roles,
            'permissions' => $user->permissions,
        ],
    ], 200);
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

  public function updateProfilePhoto(Request $request, $id)
{
    // جلب المستخدم بناءً على الـ ID
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // التحقق من صحة الملف
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048', // أقصى حجم 2MB
    ]);

    // حذف الصورة القديمة إذا كانت موجودة
    if ($user->profile_photo_path && Storage::exists($user->profile_photo_path)) {
        Storage::delete($user->profile_photo_path);
    }

    // رفع الصورة الجديدة
    $path = $request->file('profile_photo')->store('profile-photos', 'public');

    // تحديث مسار الصورة في قاعدة البيانات
    $user->profile_photo_path = $path;
    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'Profile photo updated successfully.',
        'data' => [
           'profile_photo_url' => asset('storage/' . $path),

        ],
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
