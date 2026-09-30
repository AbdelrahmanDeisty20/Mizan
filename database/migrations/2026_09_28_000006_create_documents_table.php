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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('legal_case_id')->nullable()->constrained('legal_cases')->cascadeOnDelete();
            $table->string('title'); // عنوان المستند
            $table->string('file_path'); // مسار الملف
            $table->string('file_type')->nullable(); // pdf, image, doc
            $table->longText('extracted_text')->nullable(); // النص المستخرج بالـ OCR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
