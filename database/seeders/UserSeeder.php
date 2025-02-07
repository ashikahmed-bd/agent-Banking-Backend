<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Ashik Ahmed',
                'phone' => '01911742233',
                'email' => 'info@ashikahmed.net',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => UserType::ADMIN,
            ],

            [
                'name' => 'Maidul Islam',
                'phone' => '01917360036',
                'email' => 'maidul@ashikahmed.net',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => UserType::VENDOR,
            ],
        ]);
    }
}
