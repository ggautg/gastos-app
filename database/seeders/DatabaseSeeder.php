<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\HouseholdUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $household = Household::create(['name' => 'Finanzas de '.$user->name]);

        HouseholdUser::create([
            'household_id' => $household->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $user->useHousehold($household);
    }
}
