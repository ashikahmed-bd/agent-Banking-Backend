<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
    protected $fillable = [
        'name', 'number', 'logo', 'initial_balance', 'current_balance', 'active',
    ];

    protected $hidden = [
        'disk',
        'company_id',
    ];

    public function company() {
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

    public function deposit($amount, string $note = null): void
    {
        $this->balance += $amount;

        $this->transactions()->create([
            'amount' => $amount,
            'date' => now(),
            'type' => PaymentType::CREDIT,
            'balance_after_transaction' => $this->balance,
            'note' => $note,
        ]);

        $this->save();
    }

    /**
     * @throws InsufficientBalance
     */
    public function withdraw($amount, string $note = null): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientBalance;
        }
        $this->balance -= $amount;
        $this->save();

        $this->transactions()->create([
            'amount' => $amount,
            'date' => now(),
            'type' => PaymentType::DEBIT,
            'balance_after_transaction' => $this->balance,
            'note' => $note,
        ]);
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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
