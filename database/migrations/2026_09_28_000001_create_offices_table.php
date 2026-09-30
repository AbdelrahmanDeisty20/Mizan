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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_name'); // اسم المكتب
            $table->string('syndicate_card_id')->nullable(); // رقم الكارنيه النقابي
            $table->foreignId('degree_id')->nullable()->constrained('degrees')->nullOnDelete(); // درجة القيد
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->nullOnDelete(); // المحافظة
            $table->text('office_address'); // العنوان التفصيلي
            $table->string('office_phone')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamp('trial_ends_at')->nullable(); // تاريخ انتهاء الفترة التجريبية للذكاء الاصطناعي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
