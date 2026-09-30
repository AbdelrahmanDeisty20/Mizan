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
            $table->foreignId('court_id')->nullable()->after('client_id')->constrained('courts')->nullOnDelete();
            $table->dropColumn('court_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
            $table->dropColumn('court_id');
            $table->string('court_name')->after('client_id');
        });
    }
};
