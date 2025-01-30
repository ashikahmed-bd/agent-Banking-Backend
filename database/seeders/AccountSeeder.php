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
                'number' => '',
                'logo' => 'accounts/cash.png',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '5000',
                'active' => true,
                'default' => true,
            ],
            [
                'name' => 'City Bank',
                'number' => '202236525252',
                'logo' => 'accounts/city.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '5000',
                'active' => true,
                'default' => false,
            ],
            [
                'name' => 'Islami Bank',
                'number' => '205036525252',
                'logo' => 'accounts/islami.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '5000',
                'active' => true,
                'default' => false,
            ],
            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'logo' => 'accounts/bkash.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '5000',
                'active' => true,
                'default' => false,
            ],
            [
                'name' => 'Nagad',
                'number' => '01516598533',
                'logo' => 'accounts/nagad.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '3000',
                'active' => true,
                'default' => false,
            ],
            [
                'name' => 'Rocket',
                'number' => '01516598533',
                'logo' => 'accounts/rocket.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '2500',
                'active' => true,
                'default' => false,
            ],
            [
                'name' => 'Upay',
                'number' => '01516598533',
                'logo' => 'accounts/upay.svg',
                'banner' => 'accounts/bg-bkash.png',
                'balance' => '1000',
                'active' => true,
                'default' => false,
            ],
        ]);
    }
}
