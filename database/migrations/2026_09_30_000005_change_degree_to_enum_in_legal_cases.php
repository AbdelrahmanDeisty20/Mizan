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
            $table->dropColumn('degree');
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            $table->enum('degree', [
                'primary',      // ابتدائي
                'appeal',       // استئناف
                'cassation',    // نقض
            ])->default('primary')->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn('degree');
        });

        Schema::table('legal_cases', function (Blueprint $table) {
            $table->string('degree')->default('ابتدائي')->after('year');
        });
    }
};
