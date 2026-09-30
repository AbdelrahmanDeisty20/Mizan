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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('assigned_office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->string('title'); // نوع الخدمة المطلوب تنفيذها (حضور جلسة، سحب مستند، تصوير ملف...)
            $table->text('description'); // التفاصيل
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->nullOnDelete(); // المحافظة
            $table->string('court_name'); // المحكمة
            $table->date('due_date'); // الموعد النهائي
            $table->decimal('offered_fee', 10, 2); // المقابل الرمزي
            $table->string('status')->default('open'); // open, in_progress, completed, cancelled
            $table->string('contact_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
