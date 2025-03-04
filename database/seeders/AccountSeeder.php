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
                'opening_balance' => '50000',
                'current_balance' => '50000',
                'default' => true,
                'company_id' => Company::query()->firstOrFail()->id,
                'created_by' => User::query()->firstOrFail()->id,
            ],

            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'opening_balance' => '50000',
                'current_balance' => '50000',
                'default' => false,
                'company_id' => Company::query()->firstOrFail()->id,
                'created_by' => User::query()->firstOrFail()->id,
            ],
        ]);
    }
}
