<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('doctors')->insert([
        //     'user_id' => random_int(1,10),
        //     'contact' => '0000000000',
        //     'gender' => 'Male',
        //     'dob' => '2021-01-01',
        //     'address' => Str::random(10),
        // ]);
        Doctor::factory()
            ->count(50)
            ->create();

    
    }
}
