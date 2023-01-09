<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductClassBRequest;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

use App\Services\BrandService;

use App\Http\Requests\ProductRequest;
use App\Jobs\ProcessScrapedData;
use App\Models\Collection;
use App\Models\Color;
use App\Models\ProductMedia;
use App\Models\Quantity;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ProductController extends Controller
{
    use ResponseTraits;
    protected $product_service;
    protected $brand_service;

    public function __construct(ProductService $service, BrandService $b_service)
    {
        $this->brand_service = $b_service;
        $this->product_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $data = $this->product_service->index();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $e;
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the filtered resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filterProducts(Request $request)
    {

        try {
            $data = $this->product_service->filterProducts($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function skus(Request $req)
    {
        try {
            $data = $this->product_service->skus($req->query('search', ''));
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDeleted()
    {
        try {
            $data = $this->product_service->indexDeleted();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // $all_brands = $this->brand_service->index();
        //  return dd($all_brands);
        try {

            DB::transaction(function () use ($request) {

                $data = $this->product_service->store($request);

                if ($request->brand_id) {

                    $request->validate([
                        'original_price' => "required|numeric",
                        'discount_price' => "required|numeric"
                    ]);

                    $brand = $this->brand_service->show($request->brand_id);
                    $this->brand_service->check_product_price($data, $brand);
                }
            });


            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->product_service->show($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            DB::transaction(function () use ($request, $id) {

                $data = $this->product_service->update($request, $id);
                //return $data;
                if ($data->brand_id) {

                    $brand = $this->brand_service->show($data->brand_id);
                    $this->brand_service->check_product_price($data, $brand);
                }
            });
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->product_service->destroy($id);
            return $data;
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $data = $this->product_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\Http\Response
     */
    public function quantities($id)
    {
        try {
            $data = $this->product_service->quantities($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        try {
            $data = $this->product_service->data();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\Http\Response
     */
    public function brands()
    {
        try {
            $data = $this->product_service->brands();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __($e), null, 400);
        }
    }
    /**
     * Display a listing of the genders.
     *
     * @return \Illuminate\Http\Response
     */
    public function genders()
    {
        try {
            $data = $this->product_service->genders();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the colors.
     *
     * @return \Illuminate\Http\Response
     */

    public function colors()
    {
        try {
            $data = $this->product_service->colors();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display a listing of the offers.
     *
     * @return \Illuminate\Http\Response
     */
    public function offers()
    {
        try {
            $data = $this->product_service->offers();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the main Categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function mainCategories($gender)
    {
        try {
            $data = $this->product_service->mainCategories($gender);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the sub Categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function subCategories($category)
    {
        try {
            $data = $this->product_service->subCategories($category);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display a listing of the resource with Filtering.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        try {
            $data = $this->product_service->filter($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    public function search(Request $request)
    {
        try {
            $data = $this->product_service->search($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the resource with Filtering.
     *
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        // try {
        $data = $this->product_service->report($request);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }


    /************************** Class B **********************************/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function classBIndex(Request $request)
    {
        try {
            $data = $this->product_service->classBIndex($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function classBStore(ProductClassBRequest $request)
    {
        try {
            $data = $this->product_service->classBStore($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function classBShow($id)
    {
        try {
            $data = $this->product_service->classBShow($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function classBUpdate(Request $request, $id)
    {
        try {
            $data = $this->product_service->classBUpdate($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function classBDestroy($id)
    {
        try {
            $data = $this->product_service->classBDestroy($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * get product Details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productDetails($id)
    {

        $model = Product::find($id);
        if ($model->is_new !== null && $model->brand_id == 25) {

            do {
                $ch = curl_init("https://www.zara.com/tr/en/products-details?productIds=" . $model['OEM'] . "&ajax=true");
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
            // dd($api_product_response);
            if (isset($api_product_response) && count($api_product_response)) {

                $api_product_response = $api_product_response[0];
                $product_details = $api_product_response;
                $this->handelProductUpdate(Product::find($id), $product_details);
            }
        }

        try {
            //check the last time the product was updated

            $lastUpdate = new Carbon($model->updated_at);
            $diff = $lastUpdate->diffInMinutes(Carbon::now());


            // update only if 5 min elapsed since the last update to make sure we dont get baned
            if ($diff > 5) {
                $class =  "\App\Events" . "\\" . Str::studly(strtolower($model->brand->name)) . 'ProductRequest';
                $class::dispatch($model);

                $model->updated_at = Carbon::now();
                $model->save();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        // try {
        $data = $this->product_service->productDetails($id);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }

    function color_name_to_hex($color_name)
    {
        $color_name = str_replace("-", " ", $color_name);
        // standard 147 HTML color names
        $colors  =  array(
            'aliceblue' => 'F0F8FF',
            'antiquewhite' => 'FAEBD7',
            'aqua' => '00FFFF',
            'aquamarine' => '7FFFD4',
            'azure' => 'F0FFFF',
            'beige' => 'F5F5DC',
            'bisque' => 'FFE4C4',
            'black' => '000000',
            'blanchedalmond ' => 'FFEBCD',
            'blue' => '0000FF',
            'blueviolet' => '8A2BE2',
            'brown' => 'A52A2A',
            'burlywood' => 'DEB887',
            'cadetblue' => '5F9EA0',
            'chartreuse' => '7FFF00',
            'chocolate' => 'D2691E',
            'coral' => 'FF7F50',
            'cornflowerblue' => '6495ED',
            'cornsilk' => 'FFF8DC',
            'crimson' => 'DC143C',
            'cyan' => '00FFFF',
            'darkblue' => '00008B',
            'darkcyan' => '008B8B',
            'darkgoldenrod' => 'B8860B',
            'darkgray' => 'A9A9A9',
            'darkgreen' => '006400',
            'darkgrey' => 'A9A9A9',
            'darkkhaki' => 'BDB76B',
            'darkmagenta' => '8B008B',
            'darkolivegreen' => '556B2F',
            'darkorange' => 'FF8C00',
            'darkorchid' => '9932CC',
            'darkred' => '8B0000',
            'darksalmon' => 'E9967A',
            'darkseagreen' => '8FBC8F',
            'darkslateblue' => '483D8B',
            'darkslategray' => '2F4F4F',
            'darkslategrey' => '2F4F4F',
            'darkturquoise' => '00CED1',
            'darkviolet' => '9400D3',
            'deeppink' => 'FF1493',
            'deepskyblue' => '00BFFF',
            'dimgray' => '696969',
            'dimgrey' => '696969',
            'dodgerblue' => '1E90FF',
            'firebrick' => 'B22222',
            'floralwhite' => 'FFFAF0',
            'forestgreen' => '228B22',
            'fuchsia' => 'FF00FF',
            'gainsboro' => 'DCDCDC',
            'ghostwhite' => 'F8F8FF',
            'gold' => 'FFD700',
            'goldenrod' => 'DAA520',
            'gray' => '808080',
            'green' => '008000',
            'greenyellow' => 'ADFF2F',
            'grey' => '808080',
            'honeydew' => 'F0FFF0',
            'hotpink' => 'FF69B4',
            'indianred' => 'CD5C5C',
            'indigo' => '4B0082',
            'ivory' => 'FFFFF0',
            'khaki' => 'F0E68C',
            'lavender' => 'E6E6FA',
            'lavenderblush' => 'FFF0F5',
            'lawngreen' => '7CFC00',
            'lemonchiffon' => 'FFFACD',
            'lightblue' => 'ADD8E6',
            'lightcoral' => 'F08080',
            'lightcyan' => 'E0FFFF',
            'lightgoldenrodyellow' => 'FAFAD2',
            'lightgray' => 'D3D3D3',
            'lightgreen' => '90EE90',
            'lightgrey' => 'D3D3D3',
            'lightpink' => 'FFB6C1',
            'lightsalmon' => 'FFA07A',
            'lightseagreen' => '20B2AA',
            'lightskyblue' => '87CEFA',
            'lightslategray' => '778899',
            'lightslategrey' => '778899',
            'lightsteelblue' => 'B0C4DE',
            'lightyellow' => 'FFFFE0',
            'lime' => '00FF00',
            'limegreen' => '32CD32',
            'linen' => 'FAF0E6',
            'magenta' => 'FF00FF',
            'maroon' => '800000',
            'mediumaquamarine' => '66CDAA',
            'mediumblue' => '0000CD',
            'mediumorchid' => 'BA55D3',
            'mediumpurple' => '9370D0',
            'mediumseagreen' => '3CB371',
            'mediumslateblue' => '7B68EE',
            'mediumspringgreen' => '00FA9A',
            'mediumturquoise' => '48D1CC',
            'mediumvioletred' => 'C71585',
            'midnightblue' => '191970',
            'mintcream' => 'F5FFFA',
            'mistyrose' => 'FFE4E1',
            'moccasin' => 'FFE4B5',
            'navajowhite' => 'FFDEAD',
            'navy' => '000080',
            'oldlace' => 'FDF5E6',
            'olive' => '808000',
            'olivedrab' => '6B8E23',
            'orange' => 'FFA500',
            'orangered' => 'FF4500',
            'orchid' => 'DA70D6',
            'palegoldenrod' => 'EEE8AA',
            'palegreen' => '98FB98',
            'paleturquoise' => 'AFEEEE',
            'palevioletred' => 'DB7093',
            'papayawhip' => 'FFEFD5',
            'peachpuff' => 'FFDAB9',
            'peru' => 'CD853F',
            'pink' => 'FFC0CB',
            'plum' => 'DDA0DD',
            'powderblue' => 'B0E0E6',
            'purple' => '800080',
            'red' => 'FF0000',
            'rosybrown' => 'BC8F8F',
            'royalblue' => '4169E1',
            'saddlebrown' => '8B4513',
            'salmon' => 'FA8072',
            'sandybrown' => 'F4A460',
            'seagreen' => '2E8B57',
            'seashell' => 'FFF5EE',
            'sienna' => 'A0522D',
            'silver' => 'C0C0C0',
            'skyblue' => '87CEEB',
            'slateblue' => '6A5ACD',
            'slategray' => '708090',
            'slategrey' => '708090',
            'snow' => 'FFFAFA',
            'springgreen' => '00FF7F',
            'steelblue' => '4682B4',
            'tan' => 'D2B48C',
            'teal' => '008080',
            'thistle' => 'D8BFD8',
            'tomato' => 'FF6347',
            'turquoise' => '40E0D0',
            'violet' => 'EE82EE',
            'wheat' => 'F5DEB3',
            'white' => 'FFFFFF',
            'whitesmoke' => 'F5F5F5',
            'yellow' => 'FFFF00',
            'yellowgreen' => '9ACD32'
        );

        $color_name = strtolower($color_name);



        $color_result = $this->array_partial_search($colors, $color_name);
        $has_result = count($color_result);
        if ($has_result) {
            return ('#' . $color_result[0]);
        } else {
            return ('#' . 'FFFFFF');
        }
    }

    function array_partial_search($array, $keyword)
    {
        $keyword =  strtolower($keyword);
        $found = [];

        // Loop through each item and check for a match.
        foreach ($array as $string => $value) {
            // If found somewhere inside the string, add.
            $words = explode(' ', $keyword);
            foreach ($words as $word) {
                if (strpos($word, $string) !== false) {
                    $found[] = $value;
                }
            }
        }

        return $found;
    }

    public function handelProductUpdate(Product $model, $product_details)
    {


        //update product meadia
        foreach ($product_details['detail']['colors'] as $color) {
            $color_model = Color::firstOrCreate([
                'color_value' => $this->color_name_to_hex($color['name']),
                'name' => $color['name'],
            ]);

            $media_exist = $model->medias()->where([
                "color_id" => $color_model->id
            ])->first();

            // if media dosen't exist , just add it

            if (!$media_exist) {
                foreach ($color['xmedia'] as $media) {
                    ProductMedia::create([
                        "product_id" => $model->id,
                        "color_id" => $color_model->id,
                        "path" => $media['path']
                    ]);
                }
            }


            //update product quantities
            foreach ($color['sizes'] as $size) {
                $quantity = $model->quantities()->where([
                    "size" => $size['name'],
                    "color_id" => $color_model->id,
                ])->first();

                //update the quantity
                if ($quantity) {

                    $quantity->update([
                        "quantity" => ($size['availability'] == "in_stock" ? 1000000 : 0)
                    ]);

                    // else create it
                } else {
                    Quantity::create([
                        "size" => $size['name'],
                        "color_id" => $color_model->id,
                        "quantity" => ($size['availability'] == "in_stock" ? 1000000 : 0),
                        "product_id" => $model->id
                    ]);
                }
            }
        }
    }


    public function skuProductDetails($sku)
    {
        // try {
        $data = $this->product_service->skuProductDetails($sku);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }

    public function scrape()
    {
        ProcessScrapedData::dispatch($this->product_service, $this->brand_service);

        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
}