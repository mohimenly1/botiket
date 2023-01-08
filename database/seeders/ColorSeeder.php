<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->insert([
            'name' => 'أبيض',
            'color_value' => 'FFFFFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

               DB::table('colors')->insert([
            'name' => 'أسود',
            'color_value' => '000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

               DB::table('colors')->insert([
            'name' => 'رمادي',
            'color_value' => '666666',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'بيج',
            'color_value' => 'ddddc6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'أحمر',
            'color_value' => 'cc0000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'كستنائي',
            'color_value' => '660000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'بني',
            'color_value' => '783c00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'قرمزي',
            'color_value' => '7a0000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'New York Pink ',
            'color_value' => '80CCCC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Alabaster',
            'color_value' => 'E6F5F5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Sweet Brown',
            'color_value' => 'ad3333',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'وردي',
            'color_value' => 'FFC0CB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Parrot Pink',
            'color_value' => 'cc9aa2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'برتقالي',
            'color_value' => 'ffae1a',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'أصفر',
            'color_value' => 'ffd780',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'ذهبي',
            'color_value' => 'e6c200',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'زيتي',
            'color_value' => '737300',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'ليموني',
            'color_value' => 'cce600',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'أخضر',
            'color_value' => '00560e',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Ash Gray',
            'color_value' => 'b3ccb7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'أزرق',
            'color_value' => '1e87db',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'بنفسجي',
            'color_value' => '8c239e',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Russian Violet',
            'color_value' => '3e1046',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Medium Lavender Magenta ',
            'color_value' => 'd7a9df',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('colors')->insert([
            'name' => 'أرجواني',
            'color_value' => 'cc0073',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('colors')->insert([
            'name' => 'Rose Garnet',
            'color_value' => '990056',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        
    }
}
