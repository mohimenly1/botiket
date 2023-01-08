<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestGenratorTest extends TestCase
{
 // use RefreshDatabase;

 public function requestGenrator($product, $brand_id, $gender_id)
 {

     $data = (object)[
         'sku' => $product->sku,
         'title' => $product->name,
         'description' => $product->description,
         'OEM' => $product->sku,
         'price' => $product->price,
         'is_shipped' => true,
         'is_featured' => false,
         'brand_id' => $brand_id,
         'gender_id' => $gender_id,
         'medias' => [],
         'quantities' => []
     ];

     $data->quantities = [];
     $data->medias = [];





  


     $combinations = collect($product->combinations);
     
     

     $combinations->map(function($combination, $key){
      $combination->color = $combination->features[0]->value;
      $combination->size = $combination->features[1]->value;;
      //dd($combination);
      return $combination;
     });


     $combinations->each(function( $combination ) use($data) {
         
      
       array_push($data->quantities,[
             'size' => $combination->size,
             'quantity' => $combination->quantity,
             'color' => $combination->color
         ]);

     });

     $combinations = $combinations->groupBy('color');
     

     $combinations->each(function( $combination ) use($data) {
         
      $images = collect($combination[0]->images);
         $images->each( function($img) use($data,$combination) {
          $x = [
            'file' => $img->url,
            'color' => $combination[0]->color
          ];
          
          array_push($data->medias,$x);
          
          
         });
         
         

     });

     dd($data);
    return $data;


 }

  public function test_req_genrator()
  {
      $product = json_decode(' {
        "sku": "07559358",
        "name": "CROPPED LINEN OVERSHIRT",
        "category_id": "1000",
        "quantity": 10,
        "description": "MATERIALZUSAMMENSETZUNG, PFLEGE UND HERKUNFT\n\tJOIN LIFE\n\tCare for fiber: 100 % Leinen aus europäischem Anbau.<br><br>Unter dem Namen „Join Life“ kennzeichnen wir die Kleidungsstücke, die mit Technologien und Rohstoffen hergestellt werden, die uns helfen, die Umweltbelastung unserer Produkte zu verringern. \n\tMATERIALZUSAMMENSETZUNG\n\tWir arbeiten mit Überwachungsprogrammen, um die Einhaltung der Sicherheits-, Gesundheits- und Qualitätsstandards unserer Produkte zu gewährleisten.<br><br>Der Standard Green to Wear 2.0 zielt darauf ab, die Umweltauswirkungen der Textilproduktion zu minimieren. Zu diesem Zweck haben wir das Programm The List von Inditex entwickelt, das uns hilft, sowohl die Sauberkeit der Produktionsprozesse als auch die Sicherheit und gesundheitliche Unbedenklichkeit unserer Kleidungsstücke zu gewährleisten.\n\tAUSSEN\n\t100% leinen\n\tFUTTER\n\t65% polyester · 35% baumwolle\n\tLeinen aus europäischem Anbau\n\tIn Europa angebautes Leinen ist eine natürliche Pflanzenfaser, die in Frankreich, Belgien und den Niederlanden angebaut wird.<br><br>Die Produktion folgt dem European Flax®-Standard, der von der European Confederation of Flax and Hemp (CELC), der föderalen Behörde für die Leinen-Agrarindustrie in der Welt, definiert wurde.<br><br>Der Anbau erfolgt in Fruchtfolge, mit wenigen Pestiziden und Düngemitteln und ohne künstliche Bewässerung*, transgenes Saatgut oder Entlaubungsmittel. Diese Techniken reduzieren die Auswirkungen auf den Boden, die landwirtschaftliche Biodiversität und die Süßwasserressourcen. \n\tZERTIFIZIERUNGEN\n\tDer Produktionsprozess der Faser ist von European Flax® der European Confederation of Linen and Hemp (CELC – Europäisches Bündnis für Leinen und Hanf) zertifiziert, einer Non-Profit-Organisation, die alle Stufen der Produktion und Verarbeitung von Leinen und Hanf überwacht.\n\tVORTEILE FÜR DIE UMWELT\n\tPFLEGE\n\tPflege Ihrer Kleidung bedeutet Pflege der Umwelt.\n\tUm Ihre Jacken und Mäntel sauber zu halten, müssen Sie diese nur lüften und sie mit einem Tuch oder einer Bürste leicht reinigen. Wenn eine chemische Reinigung erforderlich ist, versuchen Sie, Reinigungen zu finden, die umweltfreundliche Technologien einsetzen.\n\tHERKUNFT\n\tWir arbeiten mit unseren Lieferanten, Arbeitnehmern/innen, Gewerkschaften und internationalen Organisationen zusammen, um eine Lieferkette zu entwickeln, in der die Menschenrechte respektiert und gefördert werden und die zu den Zielen der Vereinten Nationen für nachhaltige Entwicklung beiträgt. <br><br>Darüber hinaus haben wir dank der kontinuierlichen Zusammenarbeit mit unseren Lieferanten ein Rückverfolgbarkeitsprogramm entwickelt, mit dem wir herausfinden können, wo und wie unsere Kleidungsstücke hergestellt werden.\n\tHergestellt in Marokko\n\t",
        "short_description": "",
        "manufacturer": "ZARA",
        "mpn": "",
        "weight": "0",
        "height": "0",
        "width": "0",
        "depth": "0",
        "meta_title": "",
        "meta_keywords": "",
        "meta_description": "",
        "product_url": "https://www.zara.com/tr/en/cropped-linen-overshirt-p07559358.html",
        "reviews_number": "",
        "rating": "",
        "price": "579.95",
        "price_old": "0",
        "price_wholesale": "0",
        "images": [
          {
            "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
          },
          {
            "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
          },
          {
            "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
          },
          {
            "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
          },
          {
            "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
          },
          {
            "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
          },
          {
            "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
          },
          {
            "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
            "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
          },
          {
            "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
            "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
          }
        ],
        "combinations": [
          {
            "sku": "07559358-712-1",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Ecru"
              },
              {
                "name": "Größe",
                "value": "XS"
              }
            ],
            "images": [
              {
                "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
              },
              {
                "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
              },
              {
                "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
              },
              {
                "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
              },
              {
                "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
              },
              {
                "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
              },
              {
                "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
              },
              {
                "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-712-2",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Ecru"
              },
              {
                "name": "Größe",
                "value": "S"
              }
            ],
            "images": [
              {
                "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
              },
              {
                "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
              },
              {
                "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
              },
              {
                "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
              },
              {
                "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
              },
              {
                "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
              },
              {
                "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
              },
              {
                "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-712-3",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Ecru"
              },
              {
                "name": "Größe",
                "value": "M"
              }
            ],
            "images": [
              {
                "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
              },
              {
                "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
              },
              {
                "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
              },
              {
                "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
              },
              {
                "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
              },
              {
                "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
              },
              {
                "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
              },
              {
                "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-712-4",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Ecru"
              },
              {
                "name": "Größe",
                "value": "L"
              }
            ],
            "images": [
              {
                "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
              },
              {
                "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
              },
              {
                "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
              },
              {
                "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
              },
              {
                "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
              },
              {
                "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
              },
              {
                "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
              },
              {
                "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-712-5",
            "quantity": 0,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Ecru"
              },
              {
                "name": "Größe",
                "value": "XL"
              }
            ],
            "images": [
              {
                "name": "7559358712_1_1_1_-1231338075.jpg_ts=1653037903090",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_1_1_1.jpg?ts=1653037903090"
              },
              {
                "name": "7559358712_2_1_1_-582483592.jpg_ts=1653037904695",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_1_1.jpg?ts=1653037904695"
              },
              {
                "name": "7559358712_2_2_1_-485566506.jpg_ts=1653037904851",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_2_1.jpg?ts=1653037904851"
              },
              {
                "name": "7559358712_2_3_1_681821408.jpg_ts=1653037906578",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_2_3_1.jpg?ts=1653037906578"
              },
              {
                "name": "7559358712_6_1_1_-1611017881.jpg_ts=1653407392238",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_1_1.jpg?ts=1653407392238"
              },
              {
                "name": "7559358712_6_2_1_-151355781.jpg_ts=1653407391806",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_2_1.jpg?ts=1653407391806"
              },
              {
                "name": "7559358712_6_3_1_620480869.jpg_ts=1653407392894",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_3_1.jpg?ts=1653407392894"
              },
              {
                "name": "7559358712_6_4_1_-1109314098.jpg_ts=1653487790466",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/712/2/w/1484/7559358712_6_4_1.jpg?ts=1653487790466"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-743-1",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Light camel"
              },
              {
                "name": "Größe",
                "value": "XS"
              }
            ],
            "images": [
              {
                "name": "7559358743_1_1_1_565257888.jpg_ts=1653636316562",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_1_1_1.jpg?ts=1653636316562"
              },
              {
                "name": "7559358743_2_1_1_-1126364874.jpg_ts=1653636316972",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_1_1.jpg?ts=1653636316972"
              },
              {
                "name": "7559358743_2_2_1_-750064505.jpg_ts=1653636316941",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_2_1.jpg?ts=1653636316941"
              },
              {
                "name": "7559358743_2_3_1_1664136199.jpg_ts=1653636316942",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_3_1.jpg?ts=1653636316942"
              },
              {
                "name": "7559358743_2_4_1_643987453.jpg_ts=1653636316317",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_4_1.jpg?ts=1653636316317"
              },
              {
                "name": "7559358743_2_5_1_-326351681.jpg_ts=1653636316201",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_5_1.jpg?ts=1653636316201"
              },
              {
                "name": "7559358743_6_1_1_1041989298.jpg_ts=1653559863982",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_1_1.jpg?ts=1653559863982"
              },
              {
                "name": "7559358743_6_2_1_-942420417.jpg_ts=1653559862830",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_2_1.jpg?ts=1653559862830"
              },
              {
                "name": "7559358743_6_3_1_548852821.jpg_ts=1653559862425",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_3_1.jpg?ts=1653559862425"
              },
              {
                "name": "7559358743_6_4_1_1611398366.jpg_ts=1653559864158",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_4_1.jpg?ts=1653559864158"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-743-2",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Light camel"
              },
              {
                "name": "Größe",
                "value": "S"
              }
            ],
            "images": [
              {
                "name": "7559358743_1_1_1_565257888.jpg_ts=1653636316562",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_1_1_1.jpg?ts=1653636316562"
              },
              {
                "name": "7559358743_2_1_1_-1126364874.jpg_ts=1653636316972",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_1_1.jpg?ts=1653636316972"
              },
              {
                "name": "7559358743_2_2_1_-750064505.jpg_ts=1653636316941",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_2_1.jpg?ts=1653636316941"
              },
              {
                "name": "7559358743_2_3_1_1664136199.jpg_ts=1653636316942",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_3_1.jpg?ts=1653636316942"
              },
              {
                "name": "7559358743_2_4_1_643987453.jpg_ts=1653636316317",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_4_1.jpg?ts=1653636316317"
              },
              {
                "name": "7559358743_2_5_1_-326351681.jpg_ts=1653636316201",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_5_1.jpg?ts=1653636316201"
              },
              {
                "name": "7559358743_6_1_1_1041989298.jpg_ts=1653559863982",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_1_1.jpg?ts=1653559863982"
              },
              {
                "name": "7559358743_6_2_1_-942420417.jpg_ts=1653559862830",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_2_1.jpg?ts=1653559862830"
              },
              {
                "name": "7559358743_6_3_1_548852821.jpg_ts=1653559862425",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_3_1.jpg?ts=1653559862425"
              },
              {
                "name": "7559358743_6_4_1_1611398366.jpg_ts=1653559864158",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_4_1.jpg?ts=1653559864158"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-743-3",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Light camel"
              },
              {
                "name": "Größe",
                "value": "M"
              }
            ],
            "images": [
              {
                "name": "7559358743_1_1_1_565257888.jpg_ts=1653636316562",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_1_1_1.jpg?ts=1653636316562"
              },
              {
                "name": "7559358743_2_1_1_-1126364874.jpg_ts=1653636316972",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_1_1.jpg?ts=1653636316972"
              },
              {
                "name": "7559358743_2_2_1_-750064505.jpg_ts=1653636316941",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_2_1.jpg?ts=1653636316941"
              },
              {
                "name": "7559358743_2_3_1_1664136199.jpg_ts=1653636316942",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_3_1.jpg?ts=1653636316942"
              },
              {
                "name": "7559358743_2_4_1_643987453.jpg_ts=1653636316317",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_4_1.jpg?ts=1653636316317"
              },
              {
                "name": "7559358743_2_5_1_-326351681.jpg_ts=1653636316201",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_5_1.jpg?ts=1653636316201"
              },
              {
                "name": "7559358743_6_1_1_1041989298.jpg_ts=1653559863982",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_1_1.jpg?ts=1653559863982"
              },
              {
                "name": "7559358743_6_2_1_-942420417.jpg_ts=1653559862830",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_2_1.jpg?ts=1653559862830"
              },
              {
                "name": "7559358743_6_3_1_548852821.jpg_ts=1653559862425",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_3_1.jpg?ts=1653559862425"
              },
              {
                "name": "7559358743_6_4_1_1611398366.jpg_ts=1653559864158",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_4_1.jpg?ts=1653559864158"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-743-4",
            "quantity": 10,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Light camel"
              },
              {
                "name": "Größe",
                "value": "L"
              }
            ],
            "images": [
              {
                "name": "7559358743_1_1_1_565257888.jpg_ts=1653636316562",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_1_1_1.jpg?ts=1653636316562"
              },
              {
                "name": "7559358743_2_1_1_-1126364874.jpg_ts=1653636316972",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_1_1.jpg?ts=1653636316972"
              },
              {
                "name": "7559358743_2_2_1_-750064505.jpg_ts=1653636316941",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_2_1.jpg?ts=1653636316941"
              },
              {
                "name": "7559358743_2_3_1_1664136199.jpg_ts=1653636316942",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_3_1.jpg?ts=1653636316942"
              },
              {
                "name": "7559358743_2_4_1_643987453.jpg_ts=1653636316317",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_4_1.jpg?ts=1653636316317"
              },
              {
                "name": "7559358743_2_5_1_-326351681.jpg_ts=1653636316201",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_5_1.jpg?ts=1653636316201"
              },
              {
                "name": "7559358743_6_1_1_1041989298.jpg_ts=1653559863982",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_1_1.jpg?ts=1653559863982"
              },
              {
                "name": "7559358743_6_2_1_-942420417.jpg_ts=1653559862830",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_2_1.jpg?ts=1653559862830"
              },
              {
                "name": "7559358743_6_3_1_548852821.jpg_ts=1653559862425",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_3_1.jpg?ts=1653559862425"
              },
              {
                "name": "7559358743_6_4_1_1611398366.jpg_ts=1653559864158",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_4_1.jpg?ts=1653559864158"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          },
          {
            "sku": "07559358-743-5",
            "quantity": 0,
            "price": "579.95",
            "price_old": "0",
            "mpn": "",
            "features": [
              {
                "name": "Farbe",
                "value": "Light camel"
              },
              {
                "name": "Größe",
                "value": "XL"
              }
            ],
            "images": [
              {
                "name": "7559358743_1_1_1_565257888.jpg_ts=1653636316562",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_1_1_1.jpg?ts=1653636316562"
              },
              {
                "name": "7559358743_2_1_1_-1126364874.jpg_ts=1653636316972",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_1_1.jpg?ts=1653636316972"
              },
              {
                "name": "7559358743_2_2_1_-750064505.jpg_ts=1653636316941",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_2_1.jpg?ts=1653636316941"
              },
              {
                "name": "7559358743_2_3_1_1664136199.jpg_ts=1653636316942",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_3_1.jpg?ts=1653636316942"
              },
              {
                "name": "7559358743_2_4_1_643987453.jpg_ts=1653636316317",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_4_1.jpg?ts=1653636316317"
              },
              {
                "name": "7559358743_2_5_1_-326351681.jpg_ts=1653636316201",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_2_5_1.jpg?ts=1653636316201"
              },
              {
                "name": "7559358743_6_1_1_1041989298.jpg_ts=1653559863982",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_1_1.jpg?ts=1653559863982"
              },
              {
                "name": "7559358743_6_2_1_-942420417.jpg_ts=1653559862830",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_2_1.jpg?ts=1653559862830"
              },
              {
                "name": "7559358743_6_3_1_548852821.jpg_ts=1653559862425",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_3_1.jpg?ts=1653559862425"
              },
              {
                "name": "7559358743_6_4_1_1611398366.jpg_ts=1653559864158",
                "url": "https://static.zara.net/photos///2022/V/0/1/p/7559/358/743/2/w/1484/7559358743_6_4_1.jpg?ts=1653559864158"
              },
              {
                "name": "sustainability-extrainfo-label-1046_0_-1812971899.jpg_ts=1626188308607",
                "url": "https://static.zara.net/photos///contents/cm/sustainability/extrainfo/w/1484/sustainability-extrainfo-label-1046_0.jpg?ts=1626188308607"
              }
            ]
          }
        ]
      }',false);
      $this->requestGenrator($product,0,0);
  }
}
