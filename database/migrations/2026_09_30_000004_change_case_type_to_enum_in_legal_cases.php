<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn('case_type');
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            $table->enum('case_type', [
                'civil',           // مدني وتعويضات
                'criminal',        // جنح وجنايات
                'family',          // محكمة الأسرة / أحوال شخصية
                'administrative',  // مجلس دولة (قضاء إداري)
                'commercial',      // تجاري واقتصادي
                'labor',           // عمالي
            ])->after('court_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn('case_type');
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            $table->string('case_type')->after('court_id');
        });
    }
};
