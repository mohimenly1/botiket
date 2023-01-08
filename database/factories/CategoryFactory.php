<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'image' => $this->faker->image(),
            'gender_id'=>Gender::all()->random()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
