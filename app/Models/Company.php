<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    protected  $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_companies')
            ->withPivot('user_type')
            ->withTimestamps();
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

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
