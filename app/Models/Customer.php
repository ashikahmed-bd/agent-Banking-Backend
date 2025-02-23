<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $hidden = [
        'company_id',
    ];


    public function getAvatarAttribute(): string
    {
        return asset('images/default.png');
    }

    public function agent():BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @throws Exception
     */
    public function deposit(int $amount)
    {
        if ($amount <= 0) {
            throw new Exception("Invalid deposit amount.");
        }

        DB::transaction(function () use ($amount) {
            $this->increment('balance', $amount);
            $this->save();
        });

        return $this->balance;
    }

    /**
     * @throws Exception
     */
    public function withdraw($amount)
    {
        if ($amount <= 0) {
            throw new Exception("Invalid withdrawal amount.");
        }

        DB::transaction(function () use ($amount) {
            $this->decrement('balance', $amount);
            $this->save();
        });

        return $this->balance;
    }

    protected static function booted(): void
    {
        static::saving(function ($model){
            $model->created_by = Auth::id();
        });
    }
}
