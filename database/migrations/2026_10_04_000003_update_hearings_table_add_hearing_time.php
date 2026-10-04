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
            $table->date('hearing_date')->change();
            $table->time('hearing_time')->nullable()->after('hearing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dateTime('hearing_date')->change();
            $table->dropColumn('hearing_time');
        });
    }
};
