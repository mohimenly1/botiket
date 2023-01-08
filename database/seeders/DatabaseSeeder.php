<?php
namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(OrderStatuSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(GenderSeeder::class);
        $this->call(CurrencySeeder::class);



        // \App\Models\User::factory(10)->create();
        // \App\Models\Color::factory(10)->create();
        // \App\Models\Brand::factory(10)->create();
        // \App\Models\Gender::factory(10)->create();
        // \App\Models\Store::factory(10)->create();
        // \App\Models\Offer::factory(10)->create();
        // \App\Models\Category::factory(10)->create();
        // \App\Models\SubCategory::factory(10)->create();
        // \App\Models\Collection::factory(10)->create();
        // \App\Models\Delivery::factory(10)->create();
        // \App\Models\Product::factory(10)->create();
     


    }
}
