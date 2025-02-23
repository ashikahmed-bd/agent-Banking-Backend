<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            [
                'name' => 'Cash',
                'number' => '',
                'opening_balance' => '5000',
                'current_balance' => '5000',
                'default' => true,
                'agent_id' => Agent::query()->firstOrFail()->id,
                'created_by' => User::query()->firstOrFail()->id,
            ],

            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'opening_balance' => '5000',
                'current_balance' => '5000',
                'default' => false,
                'agent_id' => Agent::query()->firstOrFail()->id,
                'created_by' => User::query()->firstOrFail()->id,
            ],
        ]);
    }
}
