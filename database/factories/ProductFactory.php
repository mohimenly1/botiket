<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Offer;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_id' => Store::all()->random()->id,
            'sku'=>$this->faker->randomNumber(),
            'title'=>$this->faker->name(),
            'description'=>$this->faker->name(),
            'price'=>$this->faker->randomNumber(),
            'is_shipped'=>$this->faker->boolean(),
            'is_featured'=>$this->faker->boolean(),
            'brand_id'=>Brand::all()->random()->id,
            'category_id'=>Category::all()->random()->id,
            'sub_category_id'=>SubCategory::all()->random()->id,
            'offer_id'=>Offer::all()->random()->id,
            'gender_id'=>Gender::all()->random()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
