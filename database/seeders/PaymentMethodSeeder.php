<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert([
            'name' => 'بطاقة مصرفية',
            'number' =>  rand(1000, 9999),
            'display_qr' => 1,
            'logo' => 'بطاقة مصرفية',
            
        ]);
        DB::table('payment_methods')->insert([
            'name' => 'كاش',
            'number' =>  rand(1000, 9999),
            'display_qr' => 1,
            'logo' => 'كاش',
        ]);
        DB::table('payment_methods')->insert([
            'name' => 'سداد',
            'number' =>  rand(1000, 9999),
            'display_qr' => 1,
            'logo' => 'سداد',
        ]);
    }
}
