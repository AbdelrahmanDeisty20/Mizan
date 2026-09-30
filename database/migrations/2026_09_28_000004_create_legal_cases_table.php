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
        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('case_number'); // رقم القضية
            $table->integer('year'); // السنة
            $table->string('court_name'); // اسم المحكمة
            $table->string('degree')->default('ابتدائي'); // ابتدائي / استئناف / نقض
            $table->string('case_type'); // جنائي / مدني / تجاري / أحوال شخصية / إداري / عمالي ...
            $table->string('status')->default('جارية'); // جارية / محفوظة / منتهية / كسبت / خسرت
            $table->string('opponent_name')->nullable(); // اسم الخصم
            $table->string('opponent_lawyer')->nullable(); // محامي الخصم
            $table->decimal('total_fees', 12, 2)->default(0); // إجمالي الأتعاب
            $table->decimal('paid_fees', 12, 2)->default(0); // المدفوع
            $table->decimal('remaining_fees', 12, 2)->default(0); // الباقي
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_cases');
    }
};
