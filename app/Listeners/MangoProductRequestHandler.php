<?php

namespace App\Listeners;

use App\Actions\ColorResolver;
use App\Events\MangoProductRequest;
use App\Models\Color;
use App\Models\Quantity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MangoProductRequestHandler
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
     * @param  \App\Events\MangoProductRequest  $event
     * @return void
     */
    public function handle(MangoProductRequest $event)
    {

        try {
            $product = $event->product;

            $responseProductDetails = Http::withHeaders([
                'stock-id' => '052.TR.0.true.false.v5'
            ])->get("https://shop.mango.com/services/garments/" . "$product->OEM");
            $responseProductDetails->throw();
    
            $colors = $responseProductDetails->json("colors.colors");
    
            foreach ($colors as $key => $color) {
    
    
    
                //get the color id
                $colorName = $color['label'];
                $color_id = Color::firstOrCreate(["name" => $colorName], [
                    "color_value" => $this->colorResolver->handle($colorName)
                ]);
    
                foreach ($color["sizes"] as $key => $size) {
                    if($size['id'] !== '-1'){
    
                        Quantity::updateOrCreate([
                            "product_id" => $product->id,
                            "size" => $size['value'],
                            "color_id" => $color_id->id,
                        ], ["quantity" => "1000"]);
    
                    }
    
                }
            }       
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }


    }
}
