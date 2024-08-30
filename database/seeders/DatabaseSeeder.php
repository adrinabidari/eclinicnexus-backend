<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Service;
use App\Models\Specialization;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Specialization::factory(50)->create();
        // User::factory(60)->create();
        // Doctor::factory()
        //     ->count(50)
        //     ->create();
        // Patient::factory()
        //     ->count(50)
        //     ->create();

        // Service::factory()
        //     ->count(50)
        //     ->create();

        // Medicine::factory()
        //     ->count(50)
        //     ->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);
    }
}
