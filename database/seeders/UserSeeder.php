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
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@gmail.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Admin1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('admin1@gmail.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin2',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('admin2@gmail.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'super-admin@gmail.com',
            'password' => Hash::make('super-admin@gmail.com'),
            'role' => 'super admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Super Admin1',
            'email' => 'super-admin1@gmail.com',
            'password' => Hash::make('super-admin1@gmail.com'),
            'role' => 'super admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}