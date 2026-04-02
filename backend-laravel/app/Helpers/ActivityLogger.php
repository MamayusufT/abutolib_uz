<?php

namespace App\Helpers;

use App\Models\UserActivitie;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($type)
    {
        if (Auth::check()) {
            UserActivitie::create([
                'user_id' => Auth::id(),
                'type' => $type
            ]);
        }
    }
}
