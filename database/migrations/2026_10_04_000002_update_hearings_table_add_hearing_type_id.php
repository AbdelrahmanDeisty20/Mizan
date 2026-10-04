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
            if (Schema::hasColumn('hearings', 'hearing_type')) {
                $table->dropColumn('hearing_type');
            }
            $table->foreignId('hearing_type_id')->nullable()->after('hearing_date')->constrained('hearing_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropForeign(['hearing_type_id']);
            $table->dropColumn('hearing_type_id');
            $table->string('hearing_type')->nullable()->after('hearing_date');
        });
    }
};
