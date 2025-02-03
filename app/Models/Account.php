<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
    protected $fillable = [
        'name', 'number', 'logo', 'initial_balance', 'current_balance', 'active',
    ];

    protected $hidden = [
        'disk',
    ];

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
            'business_id' => $this->business_id,
            'user_id' => Auth::id(),
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
            'business_id' => $this->business_id,
            'user_id' => Auth::id(),
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

}
