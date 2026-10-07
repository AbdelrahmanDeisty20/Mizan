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
        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasColumn('documents', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('office_id')->constrained('clients')->nullOnDelete();
            }
            if (! Schema::hasColumn('documents', 'file_size')) {
                $table->string('file_size')->nullable()->after('file_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            }
            if (Schema::hasColumn('documents', 'file_size')) {
                $table->dropColumn('file_size');
            }
        });
    }
};
