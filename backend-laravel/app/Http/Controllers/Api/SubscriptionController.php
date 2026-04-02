<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\Subscription;
use App\Models\Settings;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plans::where('is_active', true)->get();
        $rate = Settings::where('key', 'usd_to_uzs')->first()->value ?? 12800;

        return response()->json([
            'status' => 'success',
            'exchange_rate' => $rate,
            'plans' => $plans
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:click,payme,uzum'
        ]);

        $plan = Plans::find($request->plan_id);
        $user = $request->user();

        $price_uzs = $plan->price_uzs;

        $paymentUrl = $this->generateProviderLink($request->payment_method, $plan, $user, $price_uzs);

        return response()->json([
            'status' => 'success',
            'checkout_url' => $paymentUrl,
            'amount' => $price_uzs
        ]);
    }

    public function currentSubscription(Request $request)
    {
        $subscription = Subscription::with('plan')
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription'], 404);
        }

        return response()->json(['data' => $subscription]);
    }

    public function handleWebhook(Request $request, $provider)
    {
        $validatedData = $request->all();

        $user_id = $validatedData['user_id'];
        $plan_id = $validatedData['plan_id'];
        $plan = Plans::find($plan_id);

        Subscription::updateOrCreate(
            ['user_id' => $user_id],
            [
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'remaining_tokens' => $plan->monthly_token_limit,
                'status' => 'active'
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    private function generateProviderLink($provider, $plan, $user, $amount)
    {
        $baseUrl = [
            'click' => 'https://my.click.uz/services/pay',
            'payme' => 'https://checkout.paycom.uz',
            'uzum' => 'https://checkout.uzum.uz'
        ];

        return $baseUrl[$provider] . "?" . http_build_query([
                'merchant_id' => config("services.$provider.merchant_id"),
                'amount' => $amount,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'return_url' => config('app.frontend_url') . '/dashboard'
            ]);
    }
}
