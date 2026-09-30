<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $degrees = [
            ['name_ar' => 'جدول عام / جزئي', 'name_en' => 'Partial / General'],
            ['name_ar' => 'محاكم ابتدائية', 'name_en' => 'Primary Courts'],
            ['name_ar' => 'محاكم الاستئناف العالي ومجلس الدولة', 'name_en' => 'High Appeal & State Council'],
            ['name_ar' => 'محكمة النقض والدستورية العليا', 'name_en' => 'Cassation & Supreme Constitutional Court'],
        ];

        foreach ($degrees as $deg) {
            DB::table('degrees')->updateOrInsert(
                ['name_ar' => $deg['name_ar']],
                ['name_en' => $deg['name_en'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
