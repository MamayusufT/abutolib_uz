<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $with = ['roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_id',
        'telegram_username',
        'otp_code',
        'otp_expires_at',
        'otp_type',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ─── OTP helpers ─────────────────────────────────────────

    public function generateOtp(string $type): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code'       => $code,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
            'otp_type'       => $type,
        ]);

        return $code;
    }

    public function isOtpValid(string $code): bool
    {
        return $this->otp_code === $code
            && $this->otp_expires_at
            && $this->otp_expires_at->isFuture();
    }

    public function clearOtp(): void
    {
        $this->update([
            'otp_code'       => null,
            'otp_expires_at' => null,
            'otp_type'       => null,
        ]);
    }
}
