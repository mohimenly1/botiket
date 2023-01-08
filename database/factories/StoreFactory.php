<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\City;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'logo' => $this->faker->image(),
            'has_sales' => $this->faker->boolean(),
            'is_featured' => $this->faker->boolean(),
            'class_a_access' => $this->faker->boolean(),
            'city_id' => City::all()->random()->id,
            'phone' => $this->faker->randomNumber(),
            'description' => $this->faker->name(),
            'longitude' => $this->faker->name(),
            'latitude' => $this->faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
