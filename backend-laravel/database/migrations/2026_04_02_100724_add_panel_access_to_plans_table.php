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
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('has_teacher_panel')->default(false)->after('max_pages_per_ocr');
            $table->boolean('has_admin_panel')->default(false)->after('has_teacher_panel');
            $table->boolean('priority_support')->default(false)->after('has_admin_panel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['has_teacher_panel', 'has_admin_panel', 'priority_support']);
        });
    }
};
