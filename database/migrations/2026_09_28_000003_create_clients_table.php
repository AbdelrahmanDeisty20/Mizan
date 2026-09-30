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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->string('name'); // اسم العميل
            $table->string('national_id')->nullable(); // رقم الهوية / الرقم القومي
            $table->string('phone'); // رقم الهاتف
            $table->string('whatsapp')->nullable(); // رقم الواتساب
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->nullOnDelete(); // المحافظة
            $table->text('address')->nullable(); // العنوان
            $table->string('access_code')->unique(); // كود متابعة العميل CLI-8492
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
