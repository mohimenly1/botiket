<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class OrderStatuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //1
        DB::table('order_status')->insert([
            'status' => 'قيد المراجعة',
        ]);
        //2
        DB::table('order_status')->insert([
            'status' => 'معلق',
        ]);  
        //3
        DB::table('order_status')->insert([
            'status' => 'قيد التجهيز',
        ]);
        //4
        DB::table('order_status')->insert([
            'status' => 'قيد التوصيل',
        ]);
      //5
        DB::table('order_status')->insert([
            'status' => 'تم التوصيل',
        ]);
        //6
        DB::table('order_status')->insert([
            'status' => 'تم الإلغاء',
        ]);
        //7
        DB::table('order_status')->insert([
            'status' => 'تم الرفض',
        ]); 
        
    }
}
