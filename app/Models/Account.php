<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
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

    public static function getDefaultAccount()
    {
        return self::query()->where('default', true)->firstOrFail();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


    /**
     * Get the current balance.
     *
     * @return string
     */
    public function getBalanceAttribute()
    {
        // Opening Balance
        $total = $this->opening_balance;

        // Sum Credit
        $total += $this->transactions()->where('type', '=', (PaymentType::CREDIT)->value)->sum('amount');

        // Subtract Debit
        $total -= $this->transactions()->where('type', '=', (PaymentType::DEBIT)->value)->sum('amount');

        return $total;
    }

    protected static function booted(): void
    {
        static::saving(function ($model){
            $companyId = Auth::user()->companies()->first()->id ?? null;
            if (!$companyId) {
                abort(403, trans('messages.no_company')); // Prevents saving without a company
            }
            $model->company_id = $companyId;
        });

    }
}
