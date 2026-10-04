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
            'جلسة مرافعة',
            'جلسة استجواب',
            'جلسة إثبات حضور',
            'جلسة تقديم مستندات',
            'جلسة خبير',
            'جلسة نطق بالحكم',
            'جلسة تصفية حسابات',
            'جلسة تحضير',
            'جلسة أولى',
            'جلسة صلح',
        ];

        foreach ($hearingTypes as $name) {
            DB::table('hearing_types')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
