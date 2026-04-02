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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ChatGPT Plus, Gemini Pro, Claude Elite
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_usd', 8, 2); // Dollardagi narxi
            $table->json('included_models'); // ["gpt-4o", "claude-3-5-sonnet", "gemini-1.5-pro"]
            $table->integer('monthly_token_limit'); // Virtual tokenlar
            $table->integer('max_pages_per_ocr');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
