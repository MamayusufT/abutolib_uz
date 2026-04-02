<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('telegram_username', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->role($request->role))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return UserResource::collection($users);
    }

    public function store(Request $request): UserResource
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'password'  => ['required', Password::min(8)],
            'role'      => 'nullable|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        return new UserResource($user->load('roles'));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('roles'));
    }

    public function update(Request $request, User $user): UserResource
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:20',
            'password'  => ['nullable', Password::min(8)],
            'role'      => 'nullable|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        $data['is_active'] = $request->boolean('is_active', $user->is_active);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return new UserResource($user->load('roles'));
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleActive(User $user): UserResource
    {
        $user->update(['is_active' => !$user->is_active]);

        return new UserResource($user->load('roles'));
    }

    public function syncRoles(Request $request, User $user): UserResource
    {
        $request->validate([
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($request->roles);

        return new UserResource($user->load('roles'));
    }

    public function assignRole(Request $request, User $user): UserResource
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->assignRole($request->role);

        return new UserResource($user->load('roles'));
    }

    public function removeRole(Request $request, User $user): UserResource
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->removeRole($request->role);

        return new UserResource($user->load('roles'));
    }
}
