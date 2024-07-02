<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'first_name' => 'frank',
            'last_name' => 'piano',
            'email' => 'pianop477@gmail.com',
            'phone' => '0678669000',
            'gender' => 'male',
            'email_verified_at' => null,
            'usertype' => 1,
            'status' => 1,
            'school_id' => null,
            'password' => Hash::make('shule123'),
            'image' => null,
        ]);

        DB::table('roles')->insert([
            ['role_name' => 'teacher'],
            ['role_name' => 'head teacher'],
            ['role_name' => 'academic'],
            ['role_name' => 'class_teacher']
        ]);
    }
}
