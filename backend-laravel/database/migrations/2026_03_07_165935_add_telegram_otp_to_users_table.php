<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan make:migration add_telegram_otp_to_users_table

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_id')->nullable()->unique()->after('email');
            $table->string('telegram_username')->nullable()->after('telegram_id');

            $table->string('phone')->nullable()->unique()->after('telegram_username');

            $table->string('otp_code', 6)->nullable()->after('telegram_username');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->enum('otp_type', ['email', 'telegram'])->nullable()->after('otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_id',
                'telegram_username',
                'otp_code',
                'otp_expires_at',
                'otp_type',
            ]);
        });
    }
};
