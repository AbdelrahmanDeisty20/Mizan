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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('consultation_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // المحامي المستهدف أو المحيب
            
            // طريقة الاستشارة المفضلة (بمقر المكتب، مكالمة هاتفية، اجتماع أونلاين)
            $table->string('consultation_method'); // office, phone, online / بمقر المكتب، مكالمة هاتفية، اجتماع أونلاين
            
            // اليوم والتوقيت المناسب
            $table->date('preferred_date'); // اليوم المفضل
            $table->string('preferred_time'); // التوقيت المناسب (مثل 06:00 PM)
            
            // موضوع الاستشارة أو الأسئلة المراد طرحها
            $table->text('subject');
            
            // الحالة والرد والأتعاب
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->text('reply')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
