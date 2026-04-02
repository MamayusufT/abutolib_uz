<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $roles,
        ]);
    }

    public function permissions(): JsonResponse
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'general';
        })->map(function ($group) {
            return $group->pluck('name');
        });

        return response()->json([
            'success' => true,
            'data'    => $permissions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:roles,name'],
            'guard_name'  => ['sometimes', 'string', 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rol muvaffaqiyatli yaratildi.',
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at->toDateTimeString(),
            ],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'guard_name'  => ['sometimes', 'string', 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? $role->guard_name,
        ]);

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions']);
        }

        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Rol muvaffaqiyatli yangilandi.',
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'created_at'  => $role->created_at->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol muvaffaqiyatli o\'chirildi.',
        ]);
    }
}
