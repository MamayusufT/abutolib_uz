<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserSession;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function __construct(protected TelegramService $telegram) {}

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $tokenResult = $user->createToken('auth_token');
        $this->saveSession($request, $user, $tokenResult->accessToken->id);

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $tokenResult->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email yoki parol noto\'g\'ri.'],
            ]);
        }

        $user = Auth::user();
        $activeSessions = UserSession::where('user_id', $user->id)->count();

        if ($activeSessions >= 3) {
            return response()->json([
                'error'    => 'session_limit',
                'message'  => 'Siz 3 ta qurilmadan kirgansiiz. Davom etish uchun birini tugatishi kerak.',
                'sessions' => UserSession::where('user_id', $user->id)
                    ->orderByDesc('last_active_at')
                    ->get()
                    ->map(fn($s) => [
                        'id'             => $s->id,
                        'device_name'    => $s->device_name,
                        'device_type'    => $s->device_type,
                        'browser'        => $s->browser,
                        'platform'       => $s->platform,
                        'ip_address'     => $s->ip_address,
                        'last_active_at' => $s->last_active_at,
                    ]),
            ], 429);
        }

        $tokenResult = $user->createToken('auth_token');
        $this->saveSession($request, $user, $tokenResult->accessToken->id);

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $tokenResult->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $tokenId = $request->user()->currentAccessToken()->id;

        UserSession::where('token_id', $tokenId)->delete();
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Muvaffaqiyatli chiqildi.']);
    }

    public function me(Request $request)
    {
        $tokenId = $request->user()->currentAccessToken()->id;

        UserSession::where('token_id', $tokenId)
            ->update(['last_active_at' => now()]);

        return response()->json(new UserResource($request->user()));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json(['user' => new UserResource($request->user()->fresh())]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Parol muvaffaqiyatli o\'zgartirildi.']);
    }

    public function sessions(Request $request)
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;

        $sessions = UserSession::where('user_id', $request->user()->id)
            ->orderByDesc('last_active_at')
            ->get()
            ->map(fn($session) => [
                'id'             => $session->id,
                'token_id'       => $session->token_id,
                'device_name'    => $session->device_name,
                'device_type'    => $session->device_type,
                'browser'        => $session->browser,
                'platform'       => $session->platform,
                'ip_address'     => $session->ip_address,
                'last_active_at' => $session->last_active_at,
                'is_current'     => $session->token_id == $currentTokenId,
            ]);

        return response()->json($sessions);
    }

    public function revokeSession(Request $request, $sessionId)
    {
        $session = UserSession::where('id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->user()->tokens()
            ->where('id', $session->token_id)
            ->delete();

        $session->delete();

        return response()->json(['message' => 'Seans yakunlandi.']);
    }

    public function revokeAllSessions(Request $request)
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;

        $request->user()->tokens()
            ->where('id', '!=', $currentTokenId)
            ->delete();

        UserSession::where('user_id', $request->user()->id)
            ->where('token_id', '!=', $currentTokenId)
            ->delete();

        return response()->json(['message' => 'Barcha boshqa seanslar yakunlandi.']);
    }

    public function revokeAndLogin(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string',
            'session_id' => 'required|integer',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email yoki parol noto\'g\'ri.'],
            ]);
        }

        $user = Auth::user();

        $session = UserSession::where('id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $user->tokens()->where('id', $session->token_id)->delete();
        $session->delete();

        $tokenResult = $user->createToken('token');
        $this->saveSession($request, $user, $tokenResult->accessToken->id);

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $tokenResult->plainTextToken,
        ]);
    }

    public function sendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name'     => explode('@', $request->email)[0],
                'password' => bcrypt(str()->random(16)),
            ]
        );

        if ($user->updated_at && $user->updated_at->diffInSeconds(now()) < 30) {
            $wait = 30 - $user->updated_at->diffInSeconds(now());
            return response()->json(['message' => "Iltimos, yana {$wait} soniya kuting."], 429);
        }

        $code = $user->generateOtp('email');

        try {
            Mail::to($user->email)->send(new OtpMail($code));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Email yuborishda xatolik yuz berdi.'], 500);
        }

        return response()->json(['message' => 'Kod emailga yuborildi.']);
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp_type', 'email')
            ->first();

        if (!$user || !$user->isOtpValid($request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Kod noto\'g\'ri yoki muddati o\'tgan.'],
            ]);
        }

        $user->clearOtp();
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function sendTelegramOtp(Request $request)
    {
        $request->validate([
            'telegram_username' => 'nullable|string|required_without:phone',
            'phone'             => 'nullable|string|required_without:telegram_username',
        ]);

        if ($request->filled('telegram_username')) {
            $username = ltrim($request->telegram_username, '@');
            $user     = User::where('telegram_username', $username)->first();
        } else {
            $phone = preg_replace('/[^0-9+]/', '', $request->phone);
            $user  = User::where('phone', $phone)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Hisob topilmadi. Avval botga /start yuboring.'], 404);
        }

        if (!$user->telegram_id) {
            return response()->json(['message' => 'Telegram ulanmagan. Botga /start yuboring.'], 422);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->diffInSeconds(now()) < 20) {
            return response()->json(['message' => 'Kod allaqachon yuborilgan. Biroz kuting.'], 429);
        }

        $code = $user->generateOtp('telegram');

        $this->telegram->sendMessage(
            $user->telegram_id,
            "🔐 *Kirish kodi:* `{$code}`\n\n⏱ Kod 5 daqiqa davomida amal qiladi."
        );

        return response()->json(['message' => 'Kod Telegram ga yuborildi.']);
    }

    public function verifyTelegramOtp(Request $request)
    {
        $request->validate([
            'telegram_username' => 'nullable|string|required_without:phone',
            'phone'             => 'nullable|string|required_without:telegram_username',
            'code'              => 'required|string|size:6',
        ]);

        if ($request->filled('telegram_username')) {
            $username = ltrim($request->telegram_username, '@');
            $user     = User::where('telegram_username', $username)
                ->where('otp_type', 'telegram')
                ->first();
        } else {
            $phone = preg_replace('/[^0-9+]/', '', $request->phone);
            $user  = User::where('phone', $phone)
                ->where('otp_type', 'telegram')
                ->first();
        }

        if (!$user || !$user->isOtpValid($request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Kod noto\'g\'ri yoki muddati o\'tgan.'],
            ]);
        }

        $user->clearOtp();
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    private function parseUserAgent(Request $request): array
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $deviceType = 'desktop';
        if ($agent->isPhone()) $deviceType = 'mobile';
        elseif ($agent->isTablet()) $deviceType = 'tablet';

        return [
            'device_name' => $agent->device() ?: 'Noma\'lum qurilma',
            'device_type' => $deviceType,
            'browser'     => $agent->browser() ?: 'Noma\'lum brauzer',
            'platform'    => $agent->platform() ?: 'Noma\'lum OS',
            'ip_address'  => $request->ip(),
        ];
    }

    private function saveSession(Request $request, User $user, string $tokenId): void
    {
        $ua = $this->parseUserAgent($request);

        $existing = UserSession::where('user_id', $user->id)
            ->where('ip_address', $ua['ip_address'])
            ->where('browser', $ua['browser'])
            ->where('platform', $ua['platform'])
            ->where('device_type', $ua['device_type'])
            ->first();

        if ($existing) {
            $user->tokens()->where('id', $existing->token_id)->delete();
            $existing->delete();
        }

        UserSession::create([
            'user_id'        => $user->id,
            'token_id'       => $tokenId,
            'device_name'    => $ua['device_name'],
            'device_type'    => $ua['device_type'],
            'browser'        => $ua['browser'],
            'platform'       => $ua['platform'],
            'ip_address'     => $ua['ip_address'],
            'last_active_at' => now(),
        ]);
    }
}
