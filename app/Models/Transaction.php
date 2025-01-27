<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'balance_after_transaction',
        'reference',
        'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
