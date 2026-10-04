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
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'court_name')) {
                $table->dropColumn('court_name');
            }
            if (! Schema::hasColumn('service_requests', 'court_id')) {
                $table->foreignId('court_id')->nullable()->after('governorate_id')->constrained('courts')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_requests', 'case_number')) {
                $table->string('case_number')->nullable()->after('court_id');
            }
            if (! Schema::hasColumn('service_requests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('requester_office_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_requests', 'assigned_user_id')) {
                $table->foreignId('assigned_user_id')->nullable()->after('assigned_office_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['court_id', 'case_number', 'user_id', 'assigned_user_id']);
            $table->string('court_name')->after('governorate_id');
        });
    }
};
