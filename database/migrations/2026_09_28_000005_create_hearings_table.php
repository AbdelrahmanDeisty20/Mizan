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
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('legal_case_id')->constrained('legal_cases')->cascadeOnDelete();
            $table->foreignId('assigned_lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('hearing_date'); // تاريخ ووقت الجلسة
            $table->string('court_room')->nullable(); // رقم الدائرة / القاعة
            $table->text('decision')->nullable(); // قرار الجلسة / الحكم
            $table->text('requirements')->nullable(); // المطلوب للجلسة القادمة
            $table->string('status')->default('مقبلة'); // مقبلة / مؤجلة / تم الحضور / منتهية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};
