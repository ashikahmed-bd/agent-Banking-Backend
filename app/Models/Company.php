<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    protected  $guarded = [];

    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function transactions():HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}
