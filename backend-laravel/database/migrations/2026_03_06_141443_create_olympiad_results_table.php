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
        Schema::create('olympiad_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olympiad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('skipped_questions')->default(0);
            $table->integer('total_questions')->default(0);
            $table->decimal('score_percent', 5, 2)->default(0);
            $table->integer('time_spent')->default(0);
            $table->integer('attempt')->default(1);
            $table->string('status')->default('in_progress');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['olympiad_id', 'user_id', 'attempt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olympiad_results');
    }
};
