<?php

namespace App\Listeners;

use App\Actions\ColorResolver;
use App\Models\Color;
use App\Models\Quantity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendyolProductRequestHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    private $colorResolver;

    public function __construct(ColorResolver $colorResolver)
    {
        $this->colorResolver =  $colorResolver;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle($event)
    {
        try {
            $product = $event->product;

            //fetch the product 
            $responseProductDetails = Http::get("https://public.trendyol.com/discovery-web-productgw-service/api/productDetail/" . $product->OEM);
            $responseProductDetails->throw();

            $productGroupId = $responseProductDetails->json("result.productGroupId");

            $response = Http::get("https://public.trendyol.com/discovery-web-websfxproductgroups-santral/api/v1/product-groups/" . $productGroupId);
            $response->throw();
            $colorVarients = $response->json("result.slicingAttributes");


            if (count($colorVarients) && $colorVarients[0]["displayName"] == 'Renk') {
                //not empty


                $colorVarients = $colorVarients[0]['attributes'];
                foreach ($colorVarients as $key => $color) {

                    //get the color id
                    $colorName = explode("-", $color['beautifiedName']);
                    $colorName =  $colorName[0];
                    $color_id = Color::firstOrCreate(["name"=> $colorName],[
                        "color_value"=> $this->colorResolver->handle($colorName)
                    ]);

                    $response = Http::get("https://public.trendyol.com/discovery-web-productgw-service/api/productDetail/" . $color['contents'][0]['id']);
                    $response->throw();
                    $sizeVarients = $response->json("result.variants");


                    foreach ($sizeVarients as $size) {

                            Quantity::updateOrCreate([
                                "product_id" => $product->id,
                                "size" => $size['attributeValue'] == "" ? "ستنادرد" : $size['attributeValue'] ,
                                "color_id" => $color_id->id,
                            ],["quantity" => "1000"]);
                        
                    }


                    //get the sizes


                }
            } else {
                //empty 
                //skip and loop over the varient

                $sizeVarients = $responseProductDetails->json("result.variants");

                $color_id = 1;
                $color =  $responseProductDetails->json("result.color");

                if(isset($color)){
                    $colorName = explode("-", $color);
                    $colorName =  $colorName[0];
                    $colorModel = Color::firstOrCreate(["name"=> $colorName],[
                        "color_value"=> $this->colorResolver->handle($colorName)
                    ]);
                    $color_id =  $colorModel->id;
                }
              


                foreach ($sizeVarients as $key => $size) {

                        Quantity::updateOrCreate([
                            "product_id" => $product->id,
                            "size" => $size['attributeValue'] == "" ? "ستنادرد" : $size['attributeValue'] ,
                            "color_id" => $color_id,
                            
                        ],["quantity" => "1000"]);
                    
                }
            }

        } catch (\Throwable $th) {
           Log::error($th->getMessage());
        }
        //update the database
    }




}
