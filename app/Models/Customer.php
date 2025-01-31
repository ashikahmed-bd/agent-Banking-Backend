<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'address'
    ];


    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function receivable()
    {
        return $this->hasMany(Payment::class)->whereNotNull('receivable');
    }

    public function payable()
    {
        return $this->hasMany(Payment::class)->whereNotNull('payable');
    }
}
