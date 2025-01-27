<?php

namespace Database\Seeders;

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
                'number' => '0001',
                'logo' => 'accounts/cash.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '5000',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'City Bank',
                'number' => '202236525252',
                'logo' => 'accounts/city.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '5000',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'Islami Bank',
                'number' => '205036525252',
                'logo' => 'accounts/islami.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '5000',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'logo' => 'accounts/bkash.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '5000',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'Nagad',
                'number' => '01516598533',
                'logo' => 'accounts/nagad.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '3000',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'Rocket',
                'number' => '01516598533',
                'logo' => 'accounts/rocket.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '2500',
                'current_balance' => '0',
                'active' => true,
            ],
            [
                'name' => 'Upay',
                'number' => '01516598533',
                'logo' => 'accounts/upay.svg',
                'banner' => 'accounts/bg-bkash.png',
                'initial_balance' => '1000',
                'current_balance' => '0',
                'active' => true,
            ],
        ]);
    }
}
