<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
            'name' => 'طرابلس',
            'price' => 10
        ]);

        DB::table('cities')->insert([
            'name' => 'بنغازي',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'مصراتة',
            'price' => 20
        ]);

        DB::table('cities')->insert([
            'name' => 'الزاوية',
            'price' => 20
        ]);

        DB::table('cities')->insert([
            'name' => 'زليتن',
            'price' => 20
        ]);

        DB::table('cities')->insert([
            'name' => 'البيضاء',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'اجدابيا',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'غريان',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'طبرق',
            'price' => 40
        ]);

        DB::table('cities')->insert([
            'name' => 'صبراتة',
            'price' => 25
        ]);

        DB::table('cities')->insert([
            'name' => 'سبها',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'الخمس',
            'price' => 20
        ]);

        DB::table('cities')->insert([
            'name' => 'درنة',
            'price' => 40
        ]);

        DB::table('cities')->insert([
            'name' => 'سرت',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'الجميل',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'الكفرة',
            'price' => 50
        ]);

        DB::table('cities')->insert([
            'name' => 'المرج',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'يفرن',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'ترهونة',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'مسلاتة',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'بني وليد ',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'صرمان',
            'price' => 25
        ]);

        DB::table('cities')->insert([
            'name' => 'رقدالين',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'الزنتان',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'زوارة',
            'price' => 25
        ]);

        DB::table('cities')->insert([
            'name' => 'شحات',
            'price' => 40
        ]);

        DB::table('cities')->insert([
            'name' => 'أوباري',
            'price' => 50
        ]);

        DB::table('cities')->insert([
            'name' => 'الأبيار',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'زلطن',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'القبة',
            'price' => 40
        ]);

        DB::table('cities')->insert([
            'name' => 'تاورغاء',
            'price' => 0
        ]);

        DB::table('cities')->insert([
            'name' => 'الماية',
            'price' => 0
        ]);

        DB::table('cities')->insert([
            'name' => 'مرزق',
            'price' => 60
        ]);

        DB::table('cities')->insert([
            'name' => 'البريقة',
            'price' => 30
        ]);

        DB::table('cities')->insert([
            'name' => 'هون',
            'price' => 35
        ]);

        DB::table('cities')->insert([
            'name' => 'جالو',
            'price' => 50
        ]);
        DB::table('cities')->insert([
            'name' => 'نالوت',
            'price' => 50
        ]);
        DB::table('cities')->insert([
            'name' => 'سلوق',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'مزدة',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'راس لانوف ',
            'price' => 30
        ]);
        DB::table('cities')->insert([
            'name' => 'العربان',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'ودان',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'العجيلات',
            'price' => 30
        ]);
        DB::table('cities')->insert([
            'name' => 'توكرة',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'براك',
            'price' => 40
        ]);
        DB::table('cities')->insert([
            'name' => 'غدامس',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'غات',
            'price' => 60
        ]);
        DB::table('cities')->insert([
            'name' => 'أوجلة',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'سوسة',
            'price' => 0
        ]);
        DB::table('cities')->insert([
            'name' => 'ربيانة',
            'price' => 0
        ]);
    }
}
