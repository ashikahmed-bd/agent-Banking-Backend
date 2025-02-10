<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    protected static function bootHasCompanyScope(): void
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
    }
}
