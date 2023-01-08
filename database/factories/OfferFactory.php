<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_id' => Store::all()->random()->id,
            'name'=>$this->faker->name(),
            'is_percentage'=>$this->faker->boolean(),
            'value'=>$this->faker->randomNumber(),
            'expire_date'=>$this->faker->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
