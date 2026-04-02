<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $members = TeamMember::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('role', 'like', "%{$request->search}%"))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('order')
            ->paginate($request->per_page ?? 15);

        return TeamMemberResource::collection($members);
    }

    public function store(Request $request): TeamMemberResource
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'telegram'  => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'avatar'    => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'role', 'email', 'telegram', 'instagram', 'bio', 'order']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('team', 'public');
        }

        return new TeamMemberResource(TeamMember::create($data));
    }

    public function show(TeamMember $teamMember): TeamMemberResource
    {
        return new TeamMemberResource($teamMember);
    }

    public function update(Request $request, TeamMember $teamMember): TeamMemberResource
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'telegram'  => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'bio'       => 'nullable|string',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'avatar'    => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'role', 'email', 'telegram', 'instagram', 'bio', 'order']);
        $data['is_active'] = $request->boolean('is_active', $teamMember->is_active);

        if ($request->hasFile('avatar')) {
            if ($teamMember->avatar) {
                Storage::disk('public')->delete($teamMember->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('team', 'public');
        }

        $teamMember->update($data);

        return new TeamMemberResource($teamMember);
    }

    public function destroy(TeamMember $teamMember): JsonResponse
    {
        if ($teamMember->avatar) {
            Storage::disk('public')->delete($teamMember->avatar);
        }

        $teamMember->delete();

        return response()->json(['message' => 'Team member deleted successfully']);
    }
}
