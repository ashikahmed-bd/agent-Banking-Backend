<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'company_id',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }


    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
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
