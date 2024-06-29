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
            'gender' => 'male',
            'phone' => '0678669000',
            'usertype' => 1,
            'status' => 1,
            'school_id' => '',
            'password' => Hash::make('shule123'),
            'image' => ''
        ]);
    }
}
