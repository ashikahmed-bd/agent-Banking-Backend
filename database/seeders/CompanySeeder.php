<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            'name' => 'Toha Fashion House',
            'phone' => '01911742235',
            'email' => '01911742235',
            'address' => 'Rowmari',
            'user_id' => User::query()->skip(1)->firstOrFail()->id,
        ]);
    }
}
