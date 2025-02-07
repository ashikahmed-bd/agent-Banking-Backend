<?php

namespace Database\Seeders;

use App\Models\Company;
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
                'logo' => 'cash.png',
                'opening_balance' => '5000',
                'default' => true,
                'company_id' => Company::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'logo' => 'bkash.svg',
                'opening_balance' => '5000',
                'default' => false,
                'company_id' => Company::query()->firstOrFail()->id,
            ],
        ]);
    }
}
