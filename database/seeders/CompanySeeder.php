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
            [
                'name' => 'Shawon Boutique House',
                'phone' => '01917360036',
                'address' => 'Rowmari',
            ],
            [
                'name' => 'Toha Boutique House',
                'phone' => '01717360036',
                'address' => 'Kurigram',
            ]
        ]);
    }
}
