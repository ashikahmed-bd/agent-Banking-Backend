<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function getBannerUrlAttribute()
    {
        return Storage::disk($this->disk)
            ->url($this->banner);
    }


    public function deposit($amount, ?string $note = null): void
    {
        $this->balance += $amount;

        $this->transactions()->create([
            'amount' => $amount,
            'date' => now(),
            'type' => PaymentType::DEPOSIT,
            'note' => $note,
        ]);

        $this->save();
    }

    /**
     * @throws InsufficientBalance
     */
    public function withdraw($amount, $note = null): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientBalance;
        }

        $this->transactions()->create([
            'amount' => $amount,
            'date' => now(),
            'type' => PaymentType::DEBIT,
            'note' => $note,
        ]);

        $this->balance -= $amount;
        $this->save();
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}
