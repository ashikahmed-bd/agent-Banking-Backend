<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentUser extends Model
{
    protected $fillable = ['agent_id', 'user_id', 'role'];

    public function agent() {
        return $this->belongsTo(Agent::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
