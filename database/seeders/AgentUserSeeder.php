<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AgentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('agent_users')->insert([
            [
                'agent_id' => Agent::query()->firstOrFail()->id,
                'user_id' => User::query()->firstOrFail()->id,
            ],
        ]);
    }
}
