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
    // Check if the user already exists
    $user = User::where('email', 'admin@del.ac.id')->first();

    if ($user) {
        // Update the existing user's details if needed
        $user->update([
            'name' => 'Johannes',
            'username' => 'johannes',
            'password' => Hash::make('Del@2022'), // Update password if necessary
        ]);
    } else {
        // Create the specific user if they don't exist
        User::factory()->create([
            'name' => 'Johannes',
            'username' => 'johannes',
            'email' => 'admin@del.ac.id',
            'password' => Hash::make('Del@2022'), // Hashed password
        ]);
    }

    // Optionally create additional random users
    User::factory(10)->create();

    $this->call([
        SectionSeeder::class,
    ]);
}
}
