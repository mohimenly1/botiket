<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $store = Store::create([
            'name' =>'متجر (1)',
            'logo' => rand(0, 99999),
            'has_sales' =>1,
            'is_featured' =>1,
            'class_a_access' =>1,
            'city_id' => City::all()->random()->id,
            'phone' => rand(0, 99999),
            'description' => rand(0, 99999999),
            'longitude' => rand(0, 9999999),
            'latitude' => rand(0, 99999999),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::create([
            'name' => 'مدير متجر (1)',
            'phone' => '966523456784',
            'role' => 'store-admin',
            'password' => Hash::make('12345678'),
            'image' => 'https://pngimage.net/wp-content/uploads/2018/06/logo-admin-png-5.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $store->users()->attach(['store_id' => $user->id]);
    }
}
