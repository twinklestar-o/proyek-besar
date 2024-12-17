<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create the specific user
        User::factory()->create([
            'name' => 'Johannes',
            'username' => 'johannes',
            'email' => 'admin@del.ac.id',
            'password' => Hash::make('Del@2022'), // Hashed password
        ]);

        // Optionally create additional random users
        User::factory(10)->create();

        $this->call([
            SectionSeeder::class,
        ]);
    }
}
