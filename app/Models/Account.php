<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Traits\HasCompanyScope;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
    use HasCompanyScope;
    protected $guarded = [];

    protected $hidden = [
        'disk',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getLogoUrlAttribute()
    {
        return Storage::disk($this->disk)
            ->url($this->logo);
    }

    public static function default()
    {
        return self::query()->where('default', true)->firstOrFail();
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

        if ($this->balance < $amount) {
            throw new Exception("Insufficient balance.");
        }

        DB::transaction(function () use ($amount) {
            $this->decrement('balance', $amount);
            $this->save();
        });

        return $this->balance;
    }

    public function transactions(): HasMany
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
