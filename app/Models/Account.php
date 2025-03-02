<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Account extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'disk'
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_id');
    }


    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->created_by = Auth::id();
        });
    }


}
