<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();

        $companyA = Company::query()->create([
            'name' => 'Abu Toha House',
            'phone' => '01857360036',
            'address' => 'Kurigram',
            'created_by' => 1,
            'default' => true,
        ]);

        $companyB = Company::query()->create([
            'name' => 'Shawon Boutique House',
            'phone' => '01917360036',
            'address' => 'Rowmari',
            'created_by' => 1,
            'default' => false,
        ]);

        $user->companies()->attach([$companyA->id, $companyB->id]);

    }
}
