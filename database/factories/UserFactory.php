<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => rand(10000000000, 999999999999),
            'password' =>  Hash::make('12345678'),
            'role' =>$this->faker->randomElement(['super-admin']),
            'image' => 'https://pngimage.net/wp-content/uploads/2018/06/logo-admin-png-5.png',
            'remember_token' =>$this->faker->cityPrefix(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

   
}
