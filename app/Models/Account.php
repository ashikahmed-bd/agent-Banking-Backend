<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
    protected $fillable = [
        'name', 'number', 'logo', 'initial_balance', 'current_balance', 'active',
    ];

    protected $hidden = [
        'disk',
    ];

    public function getLogoUrlAttribute()
    {
        return Storage::disk($this->disk)
            ->url($this->logo);
    }

    public function getBannerUrlAttribute()
    {
        return Storage::disk($this->disk)
            ->url($this->banner);
    }
}
