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
            $table->enum('client_role', [
                'plaintiff',   // مدعي (صاحب الدعوى)
                'defendant',   // مدعى عليه
                'appellant',   // مستأنف
                'appellee',    // مستأنف عليه
                'petitioner',  // طاعن
                'respondent',  // مطعون ضده
                'intervener',  // مدخل في الدعوى
            ])->default('plaintiff')->after('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn('client_role');
        });
    }
};
