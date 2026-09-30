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
        Schema::create('financial_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('legal_case_id')->nullable()->constrained('legal_cases')->nullOnDelete();
            $table->string('receipt_number')->unique(); // رقم السند الإيصال
            $table->decimal('amount', 12, 2); // المبلغ
            $table->string('payment_method')->default('نقدي'); // نقدي / تحويل بنكي / فودافون كاش / قسط / شيك
            $table->text('notes')->nullable();
            $table->date('date'); // تاريخ السند
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_receipts');
    }
};
