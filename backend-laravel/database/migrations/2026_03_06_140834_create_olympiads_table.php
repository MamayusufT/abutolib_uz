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
        Schema::create('olympiads', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('public');
            $table->string('name_uz');
            $table->string('lang')->default('uz');
            $table->text('description_uz')->nullable();
            $table->integer('questions_count');
            $table->dateTime('starts_at')->nullable();
            $table->integer('duration')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->integer('pass_score')->nullable();
            $table->integer('max_attempts')->nullable();
            $table->boolean('show_answers')->default(false);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olympiads');
    }
};
