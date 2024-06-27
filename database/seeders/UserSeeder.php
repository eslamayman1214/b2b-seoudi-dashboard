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
            'email' => 'admin@seoudisupermarket.com',
            'password' => Hash::make('admin@seoudisupermarket.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Admin1',
            'email' => 'admin1@seoudisupermarket.com',
            'password' => Hash::make('admin1@seoudisupermarket.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin2',
            'email' => 'admin2@seoudisupermarket.com',
            'password' => Hash::make('admin2@seoudisupermarket.com'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'super-admin@seoudisupermarket.com',
            'password' => Hash::make('super-admin@seoudisupermarket.com'),
            'role' => 'super admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Super Admin1',
            'email' => 'super-admin1@seoudisupermarket.com',
            'password' => Hash::make('super-admin1@seoudisupermarket.com'),
            'role' => 'super admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}