<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Color::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'color_value' =>$this->faker->cityPrefix(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }


   
}
