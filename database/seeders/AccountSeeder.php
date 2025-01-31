<?php

namespace Database\Seeders;

use App\Models\Business;
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
                'balance' => '5000',
                'active' => true,
                'default' => true,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'City Bank',
                'number' => '202236525252',
                'logo' => 'accounts/city.svg',
                'balance' => '5000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Islami Bank',
                'number' => '205036525252',
                'logo' => 'accounts/islami.svg',
                'balance' => '5000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Bkash',
                'number' => '01911742233',
                'logo' => 'accounts/bkash.svg',
                'balance' => '5000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Nagad',
                'number' => '01516598533',
                'logo' => 'accounts/nagad.svg',
                'balance' => '3000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Rocket',
                'number' => '01516598533',
                'logo' => 'accounts/rocket.svg',
                'balance' => '3000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
            [
                'name' => 'Upay',
                'number' => '01516598533',
                'logo' => 'accounts/upay.svg',
                'balance' => '4000',
                'active' => true,
                'default' => false,
                'business_id' => Business::query()->firstOrFail()->id,
            ],
        ]);
    }
}
