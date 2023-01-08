<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;
use PhpParser\Node\Expr\Cast\Double;


class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(__DIR__.'/DefaultCurrency.json', true);
        $data = json_decode($jsonString, false);


        foreach ($data as $base => $rate) {

            Currency::create([
                'base' => $base,
                'quote' => 'USD',
                'symbol' => $base."USD",
                "rate" => (float)(1/(float)$rate),
                "reverse_rate"=>$rate
            ]);
        }

        Currency::create([
            'base' => 'USD',
            'quote' => 'USD',
            'symbol' => "USDUSD",
            "rate" => 1,
            "reverse_rate"=>1
        ]);

    }
 
}

    

