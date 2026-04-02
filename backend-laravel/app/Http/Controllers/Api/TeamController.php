<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $team = TeamMember::where('is_active', true)
            ->orderBy('order')
            ->get();

        return TeamResource::collection($team);
    }
}
