<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HearingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hearingTypes = [
            'جلسة أولى (إعلان وحضور)',
            'جلسة مرافعة',
            'جلسة مرافعة ختامية',
            'جلسة تقديم مستندات ومذكرات',
            'جلسة استجواب',
            'جلسة سماع شهادة الشهود',
            'جلسة ورود تقرير الخبراء',
            'جلسة مناقشة الخبير',
            'جلسة حلف اليمين',
            'جلسة طعن بالتزوير',
            'جلسة معاينة',
            'جلسة صلح ووساطة',
            'جلسة حجز الدعوى للحكم',
            'جلسة النطق بالحكم',
            'جلسة إعادة للمرافعة',
            'جلسة تحضيرية',
            'جلسة فرز وتجنيب',
            'جلسة تصفية حسابات',
            'جلسة وقف اتفاقي / تعليقي',
        ];

        foreach ($hearingTypes as $name) {
            DB::table('hearing_types')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
