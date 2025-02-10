<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generateToken(): string
    {
        return Str::random(32);
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    protected static function booted(): void
    {

    }
}
