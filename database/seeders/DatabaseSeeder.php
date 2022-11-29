<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->command->info('User table seeded!');
    }
}

class UserTableSeeder extends Seeder {
    public function run()
    {
        DB::table('users')->delete();
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make("password")
        ]);
    }
}
