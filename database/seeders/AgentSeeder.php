<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('agents')->insert([
            [
                'name' => 'Shawon Boutique House',
                'phone' => '01917360036',
                'address' => 'Rowmari',
                'created_by' => 1,
            ]
        ]);
    }
}
