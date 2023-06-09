<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $adminCount = 3;
        $cashierCount = 5;

        User::factory()->count($adminCount)->create(['role' => 'administrator']);
        User::factory()->count($cashierCount)->create(['role' => 'cashier']);
    }
}
