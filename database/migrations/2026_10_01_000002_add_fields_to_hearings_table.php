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
        Schema::table('hearings', function (Blueprint $table) {
            $table->string('hearing_type')->nullable()->after('hearing_date'); // نوع وموضوع الجلسة
            $table->string('roll_number')->nullable()->after('court_room');   // رقم الرول
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropColumn(['hearing_type', 'roll_number']);
        });
    }
};
