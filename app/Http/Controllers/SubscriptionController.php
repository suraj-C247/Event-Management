<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\SubscriptionService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;   
use Illuminate\Support\Facades\Lang;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show the list of all plans for the authenticated user.
     */
    public function index(): View
    {
        $plans = $this->subscriptionService->listPlans();
        return view('subscriptions.plans', compact('plans'));
    }

    /**
     * Create a checkout session for the selected plan.
     */
    public function checkout(Request $request): JsonResponse
    {
        $result = $this->subscriptionService->createCheckoutSession($request->plan_id);

        if ($result['status']) {
            return response()->json(['checkout_url' => $result['url']]);
        }

        return response()->json(['message' => $result['message']], 400);
    }

    /**
     * Handle the success of the checkout session.
     */
    public function success(Request $request): RedirectResponse
    {   
        return redirect()->route('dashboard')->with('error', Lang::get('subscription_success'));
    }

    /**
     * Handle the cancellation of the checkout session.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('error', Lang::get('subscription_cancelled'));
    }

    /**
     * Show the user's current subscription plan.
     */
    public function myPlan(): View
    {
        $subscription = auth()->user()->subscription;
        return view('subscriptions.my-plan', compact('subscription'));
    }

    /**
     * Cancel the user's current subscription plan.
     */
    public function cancelMyPlan(Request $request): JsonResponse
    {
        $result = $this->subscriptionService->cancelSubscription(auth()->user());
        return response()->json($result);
    }
    
}
