<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'disk',
        'agent_id',
    ];


    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
