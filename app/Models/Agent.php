<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Agent extends Model
{
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function transactions():HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function booted(): void
    {
        static::saving(function ($model){
            $model->created_by = Auth::id();
        });
    }
}
