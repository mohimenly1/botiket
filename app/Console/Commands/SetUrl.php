<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seturl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
       Product::where('url',"https://www.zara.com/tr/en")->chunk(100, function($products){
            foreach ($products as $product) {
                
                 do {
                     
                $ch = curl_init("https://www.zara.com/tr/en/products-details?productIds=" . $product['OEM'] . "&ajax=true");
                
                $proxy = "smartproxy.proxycrawl.com:8012";
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'krKiymVzKxiMF8D5n6Ds7w');
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $api_product_response = curl_exec($ch);
                curl_close($ch);
                 } while ($api_product_response === false);
                
                $api_product_response = json_decode($api_product_response, true);
                if (!isset($api_product_response[0])) {
                  //  dd("gdsgdsg");
                    return null;
                }
                $api_product_response = $api_product_response[0];
                $product_details = $api_product_response;

                $ref =  explode( "-" , $product_details['detail']['reference'] ) ;
                $url = "https://www.zara.com/tr/en/".$product_details['seo']['keyword']."-p".$ref[0].".html";
                
                $product->update([
                    "url" => $url
                ]);
            
                
            }
        });
      
    }
}
