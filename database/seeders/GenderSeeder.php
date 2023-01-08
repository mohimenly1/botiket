<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Gender;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker; 

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
         $this->faker=$faker;
        $gender1 = Gender::create([
            'name' => 'رجال',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $category1 = Category::create([
            'name' => 'بنطلونات',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SubCategory::create([
            'name' => 'رسمية',
            'category_id' => $category1->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'كاجوال',
            'category_id' => $category1->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'جينز',
            'category_id' => $category1->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category1->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $category2 = Category::create([
            'name' => 'ملابس رجالية',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'فانلات و تيشرتات',
            'category_id' => $category2->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'قمصان',
            'category_id' => $category2->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'جاكيتات و المعاطف',
            'category_id' => $category2->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'لباس نوم',
            'category_id' => $category2->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $category3 = Category::create([
            'name' => 'أحذية رجالية',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'شباشب',
            'category_id' => $category3->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'بوتيل',
            'category_id' => $category3->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أحذية رسمية',
            'category_id' => $category3->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أحذية رياضية',
            'category_id' => $category3->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category4 = Category::create([
            'name' => 'حقائب رجالية',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'حقيبة ظهر',
            'category_id' => $category4->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'حقائب الكتف',
            'category_id' => $category4->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $category5 = Category::create([
            'name' => 'عطور رجالية',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'فاكهية',
            'category_id' => $category5->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'عود',
            'category_id' => $category5->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'حمضية',
            'category_id' => $category5->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'شرقية',
            'category_id' => $category5->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category6 = Category::create([
            'name' => 'نظارات',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'شمسية',
            'category_id' => $category6->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'طبية',
            'category_id' => $category6->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category7 = Category::create([
            'name' => 'ساعات',
            'image' => $this->faker->image(),
            'gender_id' => $gender1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'حديدية',
            'category_id' => $category7->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'جلد',
            'category_id' => $category7->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'رقمية',
            'category_id' => $category7->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category7->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);












        $gender2 = Gender::create([
            'name' => 'نساء',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category8 = Category::create([
            'name' => 'عبايات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'عبايات عملية',
            'category_id' => $category8->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'عبايات مناسبات',
            'category_id' => $category8->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'بشت',
            'category_id' => $category8->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category8->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category9 = Category::create([
            'name' => 'فساتين',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'فساتين متوسطة الطول',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'فساتين قصيرة',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'فساتين طويلة',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'فساتين السهرة',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'فساتين الحفلات',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'فساتين العمل',
            'category_id' => $category9->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category10 = Category::create([
            'name' => 'قمصان  و تيشرتات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'الكنزات والتيشيرتات',
            'category_id' => $category10->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'قفطان',
            'category_id' => $category10->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'قمصان',
            'category_id' => $category10->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category11 = Category::create([
            'name' => 'فانلات و البلوزات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => '',
            'category_id' => $category11->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'البلوزات',
            'category_id' => $category11->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'توب قصير',
            'category_id' => $category11->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'البلوزات الطويلة',
            'category_id' => $category11->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category12 = Category::create([
            'name' => 'بنطلونات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'بناطيل',
            'category_id' => $category12->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'بنطال رياضي',
            'category_id' => $category12->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);



        $category13 = Category::create([
            'name' => 'تنانير',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'تنانير متوسطة الطول',
            'category_id' => $category13->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'تنانير قصيرة',
            'category_id' => $category13->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'تنانير طويلة',
            'category_id' => $category13->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category14 = Category::create([
            'name' => 'جاكيتات و معاطف',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'جاكيتات',
            'category_id' => $category14->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'المعاطف',
            'category_id' => $category14->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category15 = Category::create([
            'name' => 'سترات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'السترات',
            'category_id' => $category15->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'البلوفرات',
            'category_id' => $category15->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category16 = Category::create([
            'name' => 'بيجامات وملابس نوم',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'بيجامات',
            'category_id' => $category16->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category16->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category17 = Category::create([
            'name' => 'احذية نسائية',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'أحذية فلات',
            'category_id' => $category17->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'صنادل',
            'category_id' => $category17->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أحذية كعب عالي',
            'category_id' => $category17->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'بووت',
            'category_id' => $category17->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => ' أحذية رياضية',
            'category_id' => $category17->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $category18 = Category::create([
            'name' => 'حقائب نسائية',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'حقائب ظهر',
            'category_id' => $category18->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'حقائب كتف',
            'category_id' => $category18->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category19 = Category::create([
            'name' => 'مجوهرات',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'الأقراط',
            'category_id' => $category19->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'القلائد',
            'category_id' => $category19->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'الخواتم',
            'category_id' => $category19->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'الأساور',
            'category_id' => $category19->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'المجوهرات الفاخرة',
            'category_id' => $category19->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $category20 = Category::create([
            'name' => 'ساعات نسائية',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SubCategory::create([
            'name' => 'حديدية',
            'category_id' => $category20->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'جلد',
            'category_id' => $category20->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'رقمية',
            'category_id' => $category20->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category20->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category21 = Category::create([
            'name' => 'نظارات نسائية',
            'image' => $this->faker->image(),
            'gender_id' => $gender2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'شمسية',
            'category_id' => $category21->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'طبية',
            'category_id' => $category21->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $gender3 = Gender::create([
            'name' => 'أطفال',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category22 = Category::create([
            'name' => 'أولاد',
            'image' => $this->faker->image(),
            'gender_id' => $gender3->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SubCategory::create([
            'name' => 'ملابس',
            'category_id' => $category22->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'حقائب',
            'category_id' => $category22->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أحذية',
            'category_id' => $category22->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category22->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $category23 = Category::create([
            'name' => 'بنات',
            'image' => $this->faker->image(),
            'gender_id' => $gender3->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SubCategory::create([
            'name' => 'حقائب',
            'category_id' => $category23->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أحذية',
            'category_id' => $category23->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        SubCategory::create([
            'name' => 'أخرى',
            'category_id' => $category23->id,
            'created_at' => now(),
            'updated_at' => now(),

        ]);
    }
}
