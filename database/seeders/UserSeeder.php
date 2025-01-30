<?php

namespace Database\Seeders;

use App\Enums\UserType;
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
                'email' => 'info@ashikahmed.net',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => UserType::ADMIN,
            ],
        ]);
    }
}
