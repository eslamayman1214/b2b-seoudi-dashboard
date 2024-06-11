<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Eslam Ayman',
            'email' => 'eslamayman1214@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin', // Assuming you have a role column in your users table
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Eslam ',
            'email' => 'eslam.ayman@seoudisupermarket.com',
            'password' => Hash::make('123456789'),
            'role' => 'user', // Assuming you have a role column in your users table
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}