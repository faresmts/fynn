<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
//        User::factory(100)->create();

        User::factory()->create([
            'name' => 'Matheus Fares',
            'email' => 'matheusfarz@gmail.com',
            'password' => Hash::make('abc123123')
        ]);
    }
}
