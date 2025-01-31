<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('businesses')->insert([
            'name' => 'Toha Fashion House',
            'owner_id' => User::query()->skip(1)->firstOrFail()->id,
            'phone' => '01911742235',
            'address' => 'Rowmari',
        ]);
    }
}
