<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function company() {
        return $this->belongsTo(Company::class);
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
        static::addGlobalScope('company_scope', function (Builder $builder) {
            if (Auth::check()) {
                $companyIds = Auth::user()->companies->pluck('id');

                if ($companyIds->isEmpty()) {
                    abort(403, trans('auth.no_company'));
                }

                $builder->whereIn('company_id', $companyIds);
            }
        });

        static::creating(function ($model){
            $companyId = Auth::user()->companies()->first()->id ?? null;
            if (!$companyId) {
                abort(403, trans('messages.no_company')); // Prevents saving without a company
            }
            $model->company_id = $companyId;
            $model->created_by = Auth::id();
        });
    }
}
