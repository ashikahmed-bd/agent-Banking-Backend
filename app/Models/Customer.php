<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasCompanyScope;
    protected $guarded = [];

    protected $hidden = [
        'company_id',
    ];


    public function getAvatarAttribute(): string
    {
        return asset('images/default.png');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute()
    {
        $credit = $this->payments()->where('type', '=', (PaymentType::CREDIT)->value)->sum('amount');

        // Subtract Debit
        $debit = $this->payments()->where('type', '=', (PaymentType::DEBIT)->value)->sum('amount');

        return $credit - $debit;
    }

    protected static function booted(): void
    {
        static::saving(function ($model){
            $companyId = Auth::user()->companies()->first()->id ?? null;
            if (!$companyId) {
                abort(403, trans('messages.no_company')); // Prevents saving without a company
            }
            $model->company_id = $companyId;
            $model->created_by = Auth::id();
        });
    }
}
