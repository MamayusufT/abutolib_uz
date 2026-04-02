<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    protected $casts = ['included_models' => 'array'];

    public function getPriceUzsAttribute()
    {
        $rate = Settings::where('key', 'usd_to_uzs')->first()->value ?? 12800;
        return round($this->price_usd * $rate, -2);
    }

    protected $appends = ['price_uzs'];
}
