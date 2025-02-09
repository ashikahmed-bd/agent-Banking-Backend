<?php

namespace App\Models;

use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory, HasCompanyScope;

    protected $guarded = [];

    protected $hidden = [
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
