<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
            'name' => 'مدير (1)',
            'phone' => '966523456783',
            'role' => 'super-admin',
            'password' => Hash::make('12345678'),
            'image' => 'https://pngimage.net/wp-content/uploads/2018/06/logo-admin-png-5.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'user1',
            'phone' => '966523456781',
            'role' => 'end-user',
            'password' => Hash::make('12345678'),
            'image' => 'https://pngimage.net/wp-content/uploads/2018/06/logo-admin-png-5.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    }
}
