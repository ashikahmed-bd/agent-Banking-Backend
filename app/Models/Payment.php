<?php

namespace App\Models;

use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasCompanyScope;
    protected $guarded = [];

    protected $hidden = [
        'company_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
