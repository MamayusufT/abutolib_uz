<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function getYears()
    {
        $userId = Auth::id();

        $years = DB::table('user_activities')
            ->where('user_id', $userId)
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'DESC')
            ->pluck('year');

        return response()->json($years->isEmpty() ? [date('Y')] : $years);
    }

    public function getHeatmapData(Request $request)
    {
        $userId = Auth::id();
        $year = $request->query('year', date('Y'));

        $activities = DB::table('user_activities')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json($activities);
    }
}
