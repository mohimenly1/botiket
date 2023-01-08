<?php

namespace App\Jobs;

use App\Helpers\FileHelper;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Quantity;
use App\Services\BrandService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProcessScrapedData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $product_service;
    public $brand_service;
    private $new_state;

    public function __construct(
        ProductService $product_service, BrandService $b_service
    )
    {
        $path = base_path() . "/app" . "/Jobs" . "/global.json";
        $data = json_decode(file_get_contents($path), true);
        $this->new_state = $data['new_state'];


        // $this->product_service = $product_service;
         $this->brand_service = $b_service;
    }

    public function reverseState()
    {
        $path = base_path() . "/app" . "/Jobs" . "/global.json";
        $data = json_encode([
            'new_state' => !($this->new_state)
        ]);

        file_put_contents($path, stripslashes($data));
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->HandelBrand();
        
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

    function resolve_category($p, $catagories)
    {

        $product = (object)$p;
        $category_id = $product->category_id;

        $catagories_array = array_filter($catagories, function ($v) use ($category_id) {
            return ($v->id === $category_id);
        });


        //var_dump($catagories_array);
        //dd($catagories_array[0]->url);
        $x = explode("en/", $catagories_array[array_key_first($catagories_array)]->url);
        $z = explode(".html", $x[1]);
        $m = explode("-", $z[0]);
        array_pop($m);
        $n = join("-", $m);
        $res =  $n . '-01';

        return $this->catagory_mapper[
            //$catagories_array[0]->name
            $res];
    }

    private $catagory_mapper = [
        
        /*
        'man-shirts-01' => [

            'category_id' => 2,
            'sub_category_id' => 5,
            'gender_id' => 1
        ],

        'man-blazers-01' => [

            'category_id' => 2,
            'sub_category_id' => 7,
            'gender_id' => 1
        ],

        'man-knitwear-01' => [

            'category_id' => 2,
            'sub_category_id' => 5,
            'gender_id' => 1
        ],

        'man-sweatshirts-01' => [

            'category_id' => 2,
            'sub_category_id' => 6,
            'gender_id' => 1
        ],
        'man-outerwear-01' => [

            'category_id' => 2,
            'sub_category_id' => 7,
            'gender_id' => 1
        ],

        'man-linen-01' => [

            'category_id' => 2,
            'sub_category_id' => 6,
            'gender_id' => 1
        ],
        'man-polos-01' => [

            'category_id' => 2,
            'sub_category_id' => 6,
            'gender_id' => 1
        ],
        'man-suits-01' => [

            'category_id' => 2,
            'sub_category_id' => 7,
            'gender_id' => 1
        ],
        'man-jackets-01' => [

            'category_id' => 2,
            'sub_category_id' => 7,
            'gender_id' => 1
        ],
        'man-overshirts-01' => [

            'category_id' => 2,
            'sub_category_id' => 6,
            'gender_id' => 1
        ],
        'man-jogging-01' => [

            'category_id' => 2,
            'sub_category_id' => 8,
            'gender_id' => 1
        ],
        'man-jeans-01' => [

            'category_id' => 1,
            'sub_category_id' => 3,
            'gender_id' => 1
        ],
        'man-trousers-01' => [

            'category_id' => 1,
            'sub_category_id' => 2,
            'gender_id' => 1
        ],
        'man-bermudas-01' => [

            'category_id' => 1,
            'sub_category_id' => 4,
            'gender_id' => 1
        ],
        'man-shoes-sneakers-01' => [

            'category_id' => 3,
            'sub_category_id' => 12,
            'gender_id' => 1
        ],
        'man-shoes-sandals-01' => [

            'category_id' => 3,
            'sub_category_id' => 9,
            'gender_id' => 1
        ],
        'man-shoes-moccasins-01' => [

            'category_id' => 3,
            'sub_category_id' => 11,
            'gender_id' => 1
        ],
        'man-shoes-shoes-01' => [

            'category_id' => 3,
            'sub_category_id' => 11,
            'gender_id' => 1
        ],
        'man-shoes-boots-01' => [

            'category_id' => 3,
            'sub_category_id' => 10,
            'gender_id' => 1
        ],
        'man-bags-backpacks-01' => [

            'category_id' => 4,
            'sub_category_id' => 13,
            'gender_id' => 1
        ],
        'man-bags-handbags-01' => [

            'category_id' => 4,
            'sub_category_id' => 14,
            'gender_id' => 1
        ],
        'man-bags-office-01' => [

            'category_id' => 4,
            'sub_category_id' => 14,
            'gender_id' => 1
        ],
        'man-bags-travel-01' => [

            'category_id' => 4,
            'sub_category_id' => 98,
            'gender_id' => 1
        ],
        'man-bags-leather-01' => [

            'category_id' => 4,
            'sub_category_id' => 14,
            'gender_id' => 1
        ],
        'man-accessories-perfumes-01' => [

            'category_id' => 5,
            'sub_category_id' => 15,
            'gender_id' => 1
        ],
        'man-bags-wallets-01' => [

            'category_id' => 4,
            'sub_category_id' => 86,
            'gender_id' => 1
        ],

        'man-accessories-belts-01' => [

            'category_id' => 26,
            'sub_category_id' => 87,
            'gender_id' => 1
        ],
        'man-accessories-hats-caps-01' => [

            'category_id' => 26,
            'sub_category_id' => 88,
            'gender_id' => 1
        ],
        'man-accessories-socks-01' => [

            'category_id' => 26,
            'sub_category_id' => 90,
            'gender_id' => 1
        ],
        'man-accessories-scarves-01' => [

            'category_id' => 26,
            'sub_category_id' => 94,
            'gender_id' => 1
        ],
        'man-accessories-sleepwear-01' => [

            'category_id' => 2,
            'sub_category_id' => 8,
            'gender_id' => 1
        ],*/



        'woman-dresses-mini-01' => [
            'category_id' => 9,
            'sub_category_id' => 30,
            'gender_id' => 2
        ],
        'woman-dresses-midi-01' => [
            'category_id' => 9,
            'sub_category_id' => 29,
            'gender_id' => 2
        ],
        'woman-dresses-maxi-01' => [
            'category_id' => 9,
            'sub_category_id' => 31,
            'gender_id' => 2
        ],
        'woman-dresses-party-01' => [
            'category_id' => 9,
            'sub_category_id' => 33,
            'gender_id' => 2
        ],
        'woman-dresses-camisole-01' => [
            'category_id' => 9,
            'sub_category_id' => 34,
            'gender_id' => 2
        ],
        'woman-jumpsuits-01' => [
            'category_id' => 9,
            'sub_category_id' => 96,
            'gender_id' => 2
        ],
        //فانلات و البلوزات
        'woman-shirts-01' => [
            'category_id' => 11,
            'sub_category_id' => 39,
            'gender_id' => 2
        ],
        //فانلات و البلوزات
        'woman-shirts-blouses-01' => [
            'category_id' => 11,
            'sub_category_id' => 39,
            'gender_id' => 2
        ],
        'woman-shirts-satin-01' => [
            'category_id' => 11,
            'sub_category_id' => 41,
            'gender_id' => 2
        ],
        'woman-tops-crop-01' => [
            'category_id' => 11,
            'sub_category_id' => 40,
            'gender_id' => 2
        ],
        //قمصان  و تيشرتات
        'woman-tshirts-01' => [
            'category_id' => 10,
            'sub_category_id' => 37,
            'gender_id' => 2
        ],
        //سترات
        'woman-knitwear-01' => [
            'category_id' => 15,
            'sub_category_id' => 50,
            'gender_id' => 2
        ],
        '2025482' => [
            'category_id' => 15,
            'sub_category_id' => 49,
            'gender_id' => 2
        ],
        'woman-sweatshirts-01' => [
            'category_id' => 15,
            'sub_category_id' => 49,
            'gender_id' => 2
        ],
        //بنطلونات
        'woman-jeans-wide-leg-01' => [
            'category_id' => 12,
            'sub_category_id' => 84,
            'gender_id' => 2
        ],
        'woman-jeans-skinny-01' => [
            'category_id' => 12,
            'sub_category_id' => 83,
            'gender_id' => 2
        ],
        'woman-trousers-01' => [
            'category_id' => 12,
            'sub_category_id' => 82,
            'gender_id' => 2
        ],
        'woman-trousers-flowing-01' => [
            'category_id' => 12,
            'sub_category_id' => 82,
            'gender_id' => 2
        ],
        'woman-trousers-leggings-01' => [
            'category_id' => 12,
            'sub_category_id' => 85,
            'gender_id' => 2
        ],
        'woman-trousers-joggers-01' => [
            'category_id' => 12,
            'sub_category_id' => 43,
            'gender_id' => 2
        ],
        'woman-trousers-shorts-01' => [
            'category_id' => 12,
            'sub_category_id' => 42,
            'gender_id' => 2
        ],
        //تنانير
        'woman-skirts-mini-01' => [
            'category_id' => 13,
            'sub_category_id' => 45,
            'gender_id' => 2
        ],
        'woman-skirts-midi-01' => [
            'category_id' => 13,
            'sub_category_id' => 44,
            'gender_id' => 2
        ],
        'woman-skirts-midi-01' => [
            'category_id' => 13,
            'sub_category_id' => 44,
            'gender_id' => 2
        ],
        //جاكيتات و معاطف
        'woman-outerwear-01' => [
            'category_id' => 14,
            'sub_category_id' => 48,
            'gender_id' => 2
        ],
        'woman-jacket-01' => [
            'category_id' => 14,
            'sub_category_id' => 47,
            'gender_id' => 2
        ],
        //احذية نسائية
        'woman-shoes-heeled-01' => [
            'category_id' => 17,
            'sub_category_id' => 55,
            'gender_id' => 2
        ],
        'woman-shoes-flat-01' => [
            'category_id' => 17,
            'sub_category_id' => 53,
            'gender_id' => 2
        ],

        'woman-shoes-boots-ankle-boots-01' => [
            'category_id' => 17,
            'sub_category_id' => 56,
            'gender_id' => 2
        ],
        'woman-shoes-sandals-flat-01' => [
            'category_id' => 17,
            'sub_category_id' => 54,
            'gender_id' => 2
        ],
        'woman-shoes-sandals-heeled-01' => [
            'category_id' => 17,
            'sub_category_id' => 54,
            'gender_id' => 2
        ],
        'woman-shoes-sneakers-01' => [
            'category_id' => 17,
            'sub_category_id' => 57,
            'gender_id' => 2
        ],

        //حقائب نسائية
        'woman-bags-shoulder-01' => [
            'category_id' => 18,
            'sub_category_id' => 59,
            'gender_id' => 2
        ],
        'woman-bags-party-bags-01' => [
            'category_id' => 18,
            'sub_category_id' => 81,
            'gender_id' => 2
        ],
        'woman-bags-large-01' => [
            'category_id' => 18,
            'sub_category_id' => 99,
            'gender_id' => 2
        ],
        //مجوهرات
        'woman-accessories-jewelry-01' => [
            'category_id' => 19,
            'sub_category_id' => 64,
            'gender_id' => 2
        ],

        //إكسسوارات
        'woman-accessories-01' => [
            'category_id' => 24,
            'sub_category_id' => 78,
            'gender_id' => 2
        ],
        'woman-accessories-foulards-01' => [
            'category_id' => 24,
            'sub_category_id' => 95,
            'gender_id' => 2
        ],
        'woman-accessories-belts-01' => [
            'category_id' => 24,
            'sub_category_id' => 103,
            'gender_id' => 2
        ],

        //نظارات نسائية
        'woman-accessories-sunglasses-01' => [
            'category_id' => 21,
            'sub_category_id' => 69,
            'gender_id' => 2
        ],

        //مكياج
        'woman-beauty-makeup-01' => [
            'category_id' => 30,
            'sub_category_id' => 102,
            'gender_id' => 2
        ],

        //عطور
        'woman-beauty-perfumes-01' => [
            'category_id' => 32,
            'sub_category_id' => 104,
            'gender_id' => 2
        ]
        /*,
        'kids-girl-dresses-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-tshirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-sweatshirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-basics-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-skirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-trousers-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-jeans-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-shirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-total-look-sets-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-looks-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-sporty-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-outerwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-knitwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-license-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-beachwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-ceremony-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-linen-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-shoes-01' => [
            'category_id' => 23,
            'sub_category_id' => 76,
            'gender_id' => 3
        ],
        'kids-girl-bags-01' => [
            'category_id' => 23,
            'sub_category_id' => 75,
            'gender_id' => 3
        ],
        'kids-girl-underwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-accessories-01' => [
            'category_id' => 23,
            'sub_category_id' => 77,
            'gender_id' => 3
        ],
        'kids-girl-perfumes-01' => [
            'category_id' => 23,
            'sub_category_id' => 77,
            'gender_id' => 3
        ],
        'kids-girl-editorial-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-girl-event-3-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],

        'kids-boy-tshirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-sweatshirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-basics-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-bermudas-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-trousers-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-jeans-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-shirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-trousers-joggers-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-outwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-trend-8-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-license-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-total-look-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-knitwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-beachwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-shoes-01' => [
            'category_id' => 22,
            'sub_category_id' => 73,
            'gender_id' => 3
        ],
        'kids-boy-backpacks-01' => [
            'category_id' => 22,
            'sub_category_id' => 72,
            'gender_id' => 3
        ],
        'kids-boy-underwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-accessories-01' => [
            'category_id' => 22,
            'sub_category_id' => 74,
            'gender_id' => 3
        ],
        'kids-boy-perfumes-01' => [
            'category_id' => 22,
            'sub_category_id' => 74,
            'gender_id' => 3
        ],
        'kids-boy-event-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-suits-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-boy-trend-1-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-editorial-10-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babygirl-editorial-new-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-new-in-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-dresses-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-tshirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-sweatshirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-basics-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-skirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-trousers-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-jeans-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-total-look-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-shirts-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-outerwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-knitwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-license-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-beachwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-shoes-01' => [
            'category_id' => 23,
            'sub_category_id' => 76,
            'gender_id' => 3
        ],
        'kids-babygirl-bags-01' => [
            'category_id' => 23,
            'sub_category_id' => 75,
            'gender_id' => 3
        ],
        'kids-babygirl-underwear-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-accessories-01' => [
            'category_id' => 23,
            'sub_category_id' => 77,
            'gender_id' => 3
        ],
        'kids-babygirl-perfumes-01' => [
            'category_id' => 23,
            'sub_category_id' => 77,
            'gender_id' => 3
        ],
        'kids-babygirl-special-prices-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-event-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babygirl-ceremony-01' => [
            'category_id' => 23,
            'sub_category_id' => 80,
            'gender_id' => 3
        ],
        'kids-babyboy-new-in-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-tshirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-sweatshirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-basics-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-bermudas-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-trousers-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-jeans-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-shirts-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-total-look-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-trousers-overalls-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-outerwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-knitwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-license-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-beachwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-shoes-01' => [
            'category_id' => 22,
            'sub_category_id' => 73,
            'gender_id' => 3
        ],
        'kids-babyboy-bags-01' => [
            'category_id' => 22,
            'sub_category_id' => 72,
            'gender_id' => 3
        ],
        'kids-babyboy-underwear-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-accessories-01' => [
            'category_id' => 22,
            'sub_category_id' => 74,
            'gender_id' => 3
        ],
        'kids-babyboy-perfumes-01' => [
            'category_id' => 22,
            'sub_category_id' => 74,
            'gender_id' => 3
        ],
        'kids-babyboy-special-prices-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-babyboy-editorial-1-01' => [
            'category_id' => 22,
            'sub_category_id' => 71,
            'gender_id' => 3
        ],
        'kids-newborn-new-in-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-shirts-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-trousers-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-bottoms-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-dresses-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-knitwear-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-bodysuits-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-dungarees-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-total-look-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-basics-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-sweatshirts-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-outwear-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-ceremony-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-shoes-01' => [
            'category_id' => 33,
            'sub_category_id' => 106,
            'gender_id' => 3
        ],
        'kids-newborn-accessories-01' => [
            'category_id' => 33,
            'sub_category_id' => 107,
            'gender_id' => 3
        ],
        'kids-newborn-underwear-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],
        'kids-newborn-special-prices-01' => [
            'category_id' => 33,
            'sub_category_id' => 105,
            'gender_id' => 3
        ],

        // all kids
        'kids-accessories-bags_backpacks-all-01' => [
            'category_id' => 23,
            'sub_category_id' => 75,
            'gender_id' => 3
        ],
        'kids-accessories-socks-01' => [
            'category_id' => 23,
            'sub_category_id' => 77,
            'gender_id' => 3
        ],*/

    ];

    function deleteRest($products_array)
    {
        $skus = array_map(function ($product) {
            return $product->sku;
        }, $products_array);

        Product::select()->whereNotIn('OEM', $skus)->delete();
    }

    private $model = null;

    function store($request)
    {
        //   DB::beginTransaction();
        $this->model = new Product();
        //SKU Generator
        $this->model->sku = $this->generateSKU($request->gender_id, $request->category_id, $request->sub_category_id, $request->brand_id);
        $this->model->OEM = $request->OEM;
        $this->model->is_new = $request->is_new;
        $this->model->url = $request->url;
        $this->model->title = $request->title;
        $this->model->description = $request->description;
        $this->model->price =  $request->price != null ? ceil($request->price) : $request->price;
        $this->model->original_price =  $request->original_price != null ? ceil($request->original_price) : $request->original_price;
        $this->model->discount_price =  $request->discount_price != null ? ceil($request->discount_price) : $request->discount_price;
        $this->model->is_shipped = $request->is_shipped;
        $this->model->is_featured = $request->is_featured;
        $this->model->brand_id = $request->brand_id;
        $this->model->sub_category_id = $request->sub_category_id;
        $this->model->category_id = $request->category_id;
        $this->model->gender_id = $request->gender_id;
        $this->model->offer_id = $request->offer_id;

        if ($this->model->discount_price > $this->model->price || $this->model->discount_price < ($this->model->price * 0.05))
            throw new \ErrorException('discount_price not valid: must be >=price || <= price*0.25 ');


        $user = Auth::user();
        $this->model->store_id = null;
      
        $this->model->save();

     

        if ($request->scraped) {
            foreach ($request->medias as $key => $media) {

                $color_id = Color::where('color_value', $media['color'])->first();
                if (is_null($color_id)) {
                    $color_id = Color::firstOrCreate([
                        'color_value' => $this->color_name_to_hex($media['color']),
                        'name' => $media['color'],
                    ]);
                }

                $this->model->medias()->create(
                    [
                        'path' => $media['file'],
                        'color_id' => $color_id->id
                    ]
                );
            }
        } else {
            if ($request->has('medias')) {
                foreach ($request->medias as $key => $media) {
                    $color_id = Color::where('color_value', $media['color'])->first();
                    $image_path = FileHelper::upload_file('/products/' . $this->model->id  . '/' . $this->model->sku . '-' . $key, $media['file']);
                    $this->model->medias()->create(
                        [
                            'path' => $image_path,
                            'color_id' => $color_id->id
                        ]
                    );
                }
            }
        }


        if ($request->has('quantities')) {
            foreach ($request->quantities as $quantity) {
                $color_id = Color::where('color_value', $quantity['color'])->first();
                if (is_null($color_id)) {
                    $color_id = Color::firstOrCreate([
                        'color_value' => $this->color_name_to_hex($quantity['color']),
                        'name' => $quantity['color']
                    ]);
                }
                $this->model->quantities()->create([
                    'size' => $quantity['size'],
                    'color_id' => $color_id->id,
                    'quantity' => $quantity['quantity'],
                ]);
            }
        }
        $this->model->save();
        //DB::commit();

        return $this->model;
    }


    public function generateSKU($gender, $category, $sub_category, $brand)
    {
        //generating a timestamp
        $timestamp = substr(date_timestamp_get(date_create()), 6);
        // changing gender from numbers to letters
        switch ($gender) {
            case 1:
                $gender = 'M';
                break;
            case 2:
                $gender = 'F';
                break;
            case 3:
                $gender = 'K';
                break;
        }
        // generating a unnique SKU for the product based on a blend of keys and a timestamp
        $SKU = $gender . $category . $sub_category . $brand . '-' . $timestamp;
        return $SKU;
    }

    public function deleteRestProducts()
    {
        Product::where("is_new", !$this->new_state)->delete();
        $this->reverseState();
    }

    public function HandelBrand()
    {
        do {
        // fetch categories
    $ch = curl_init("https://www.zara.com/tr/en/categories?ajax=true");

    //proxy setup
    $proxy = "smartproxy.proxycrawl.com:8012";
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'krKiymVzKxiMF8D5n6Ds7w');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $api_categories_response = curl_exec($ch);
    curl_close($ch);
    
        } while ($api_categories_response === false);
        
    $api_categories_response = json_decode($api_categories_response, true);
    $api_categories_response = $api_categories_response['categories'];
    
    $genders_allowed = [
        "MAN",
        "WOMAN",
        "KIDS"
    ];

    //loop over genders
    foreach ($api_categories_response as $gender) {
        if( in_array( $gender['name'],$genders_allowed)){

            //loop over categories
            foreach ($gender['subcategories'] as $category) {
                
                $this->HandelCategory($category);

            }

        }
    }

               //update the product
               $brands_to_update = Brand::where("id",25)->get();

               $this->brand_service->update_brands(null, $brands_to_update);

    }

    public function HandelCategory($catagory)
    {

        //

        /*
        $catagory = json_decode(
            ' {
            "id": 2025482,
            "name": "BLAZERS",
            "sectionName": "WOMAN",
            "redirectCategoryId": 2025498,
            "subcategories": [
                {
                    "id": 2025498,
                    "name": "View all",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 1055,
                        "keyword": "woman-blazers",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-VER_TODO",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2025490,
                    "name": "Textured",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 4779,
                        "keyword": "woman-blazers-textured",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-ESTRUCTURA",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2025483,
                    "name": "Black",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 2173,
                        "keyword": "woman-blazers-black",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-BLACK",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2025487,
                    "name": "Croppped",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 4410,
                        "keyword": "woman-blazers-crop",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-CROP",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2025486,
                    "name": "Waistcoats",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 4569,
                        "keyword": "woman-blazers-vest",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-CHALECOS",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2025493,
                    "name": "Linen",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 4408,
                        "keyword": "woman-blazers-linen",
                        "irrelevant": false,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-LINEN",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": false,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                },
                {
                    "id": 2046393,
                    "name": "Join Life",
                    "sectionName": "WOMAN",
                    "subcategories": [],
                    "layout": "products-category-view",
                    "contentType": "grid",
                    "gridLayout": "products-category-view",
                    "seo": {
                        "seoCategoryId": 4945,
                        "keyword": "woman-blazers-join-life",
                        "irrelevant": true,
                        "isHiddenInMenu": true
                    },
                    "attributes": {
                        "mustDisplayContent": false,
                        "displayUnfolded": false,
                        "showSubcategories": false,
                        "isDivider": false
                    },
                    "key": "V2022-MUJER-BLAZERS-JOIN_LIFE",
                    "isRedirected": false,
                    "isCurrent": false,
                    "isSelected": false,
                    "hasSubcategories": false,
                    "irrelevant": true,
                    "viewOptions": {
                        "zoom": "ZOOM1",
                        "isDefault": false,
                        "isForced": false
                    },
                    "menuLevel": 3
                }
            ],
            "layout": "products-category-view",
            "contentType": "grid",
            "gridLayout": "products-category-view",
            "seo": {
                "seoCategoryId": 1055,
                "keyword": "woman-blazers",
                "irrelevant": false,
                "isHiddenInMenu": false
            },
            "attributes": {
                "mustDisplayContent": true,
                "displayUnfolded": false,
                "showSubcategories": false,
                "isDivider": false,
                "hasAllSubcategoriesHidden": true
            },
            "key": "V2022-MUJER-BLAZERS",
            "isRedirected": true,
            "isCurrent": false,
            "isSelected": false,
            "hasSubcategories": true,
            "irrelevant": false,
            "viewOptions": {
                "zoom": "ZOOM1",
                "isDefault": false,
                "isForced": false
            },
            "menuLevel": 2
        }',
            true
        );*/
        if( !array_key_exists('seo',$catagory)  ){
           return null; 
        }

        if(  !$this->categoryResolver( $catagory['seo']['keyword']."-01" ) ){
            return null; 
         }
         
         do{
        // get the products from the api
        $ch = curl_init("https://www.zara.com/tr/en/category/"
            . $catagory['id'] .
            "/products?ajax=true");

        //proxy setup
        $proxy = "smartproxy.proxycrawl.com:8012";
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'krKiymVzKxiMF8D5n6Ds7w');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);    

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $api_products_response = curl_exec($ch);
        curl_close($ch);
    
    } while ($api_products_response === false);

        $api_products_response = json_decode($api_products_response, true);
        $api_products_response = $api_products_response['productGroups'];

        if(!isset($api_products_response[0])){
            return null;
        }
        $api_products_response = $api_products_response[0]['elements'];
        $catagory['id'] = "" . $catagory['id'] . "";

        //loop over products
        foreach ($api_products_response as $value) {
            if ($this->categoryResolver($catagory['seo']['keyword']."-01")) {
                
                if( array_key_exists("commercialComponents", $value) ){
                    $nested_products = $value['commercialComponents'];
                    foreach ($nested_products as $product) {
                        if (isset($product['price'])) {
                            DB::beginTransaction();
                            $d = $this->HandelProduct($product, $catagory);
                            DB::commit();
                            
                            if($d !== 0) Log::info($d->id);

                        }
                        
                    }
                }
                

            }
        }

      //  DB::beginTransaction();

        //delete rest
//$this->deleteRestProducts();
      //  DB::commit();


    }

    public function handelProductUpdate(Product $model, $product, $product_details)
    {
        //update product details
        $model->update([
            "original_price" => $product['price']
        ]);

        //update product meadia
        foreach ($product['detail']['colors'] as $color) {
            $color_model = Color::firstOrCreate([
                'color_value' => $this->color_name_to_hex($color['name']),
                'name' => $color['name'],
            ]);

            $media_exist = $model->medias()->where([
                "color_id" => $color_model->id
            ])->first();

            // if media dosen't exist , just add it
            
        if(!$media_exist){
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

  



    public function HandelProduct($product, $catagory)
    {

        $product['price'] = substr($product['price'], 0, -2);
       // usleep(1000000);
/*
        $ch = curl_init("https://www.zara.com/tr/en/products-details?productIds=" . $product['id'] . "&ajax=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $api_product_response = curl_exec($ch);
        curl_close($ch);
        $api_product_response = json_decode($api_product_response, true);

        if (!isset($api_product_response[0])) {
            return null;
        }
        $api_product_response = $api_product_response[0];

        $product_details = $api_product_response;
*/

        $model = Product::where([
            "OEM" => $product['id']
        ])->first();
        

        if ($model) {

            $ref =  explode("-", $product['detail']['reference']);
            $url = "https://www.zara.com/tr/en/" . $product['seo']['keyword'] . "-p" . $ref[0] . ".html";
    
            $model->update([
                "url" => $url
            ]);
            return $model;

          //  $this->handelProductUpdate($model, $product, null);
           // return 0;
        } else {
            return 0;

            $ref =  explode( "-" , $product['detail']['reference'] ) ;
            $url = "https://www.zara.com/tr/en/".$product['seo']['keyword']."-p".$ref[0].".html";

            $data = [
                'scraped' => true,
                'sku' => $product['id'],
                'title' => $product['name'],
                'description' => $product['description'],
                'OEM' => $product['id'],
                'url' => $url,
                'original_price' => $product['price'],
                'price' => $product['price'],
                'discount_price' => $product['price'],
                'offer_id' => null,
                'is_shipped' => false,
                'is_featured' => false,
                'brand_id' => 25,
                'medias' => [],
                'quantities' => [],
                'is_new' => $this->new_state
            ];

            //fill in the qunatites
            $colors = $product['detail']['colors'];
            foreach ($colors as $color) {
                

                /*
                foreach ($color['sizes'] as $size) {
                    $data['quantities'][] = [
                        'color' => $color['name'],
                        'size' => $size['name'],
                        "quantity" => ($size['availability'] === "in_stock" ? 1000000 : 0)
                    ];
                }
                */


                //fill in the meadias
                foreach ($color['xmedia'] as $media) {

                    $data['medias'][] = [
                        'file' => $this->pathResolver($media),
                        'color' => $color['name']
                    ];
                }
            }

            $data = array_merge($this->categoryResolver($catagory['seo']['keyword']."-01"), $data);

            $req = new Request();
            $req->replace($data);

            return $this->store($req);
        }
    }

    public function pathResolver($image)
    {
        $path =  "https://static.zara.net/photos//".$image['path']."/w/750/".$image['name'].".jpg?ts=".$image['timestamp'];
        return $path;
    }

    public function categoryResolver($category_id)
    {
        $exist = array_key_exists($category_id, $this->catagory_mapper);
        

        if ($exist) {
            return $this->catagory_mapper[$category_id];
        } else {
            return false;
        }
    }
}
