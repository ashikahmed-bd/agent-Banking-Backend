<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
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

    protected static function booted(): void
    {
        static::creating(function ($customer) {
            if (Auth::check()) {
                $companyId = Auth::user()->companies()->first()->id ?? null;
                if (!$companyId) {
                    abort(403, trans('messages.no_company')); // Prevents saving without a company
                }
                $customer->company_id = $companyId;
            }
        });

        // Global Scope to filter by company
        static::addGlobalScope('company_filter', function (Builder $builder) {
            if (Auth::check()) {
                $companyId = Auth::user()->companies()->pluck('id')->toArray();
                if (!$companyId) {
                    abort(403, trans('messages.no_company'));
                }
                $builder->whereIn('company_id', $companyId);
            }
        });
    }
}
