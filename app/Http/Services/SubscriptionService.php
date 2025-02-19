<?php

namespace App\Http\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Stripe\Subscription as StripeSubscription;

class SubscriptionService
{
    /**
     * Get the list of all plans for the authenticated user.
     */
    public function listPlans()
    {
        try {

            // Return all plans
            return Plan::all();
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error fetching plans: ' . $e->getMessage());
            throw new Exception('An error occurred while fetching plans.');
        }
    }

    /**
     * Create a checkout session for the selected plan.
     */ 
    public function createCheckoutSession($planId)
    {
        try {
            $plan = Plan::findOrFail($planId);
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'customer_email' => Auth::user()->email,
                'line_items' => [[
                    'price' => $plan->stripe_price_id, // Use Stripe price ID
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'metadata' => [
                    'plan_id' => $plan->id,
                    'user_id' => Auth::id(),
                ],
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel'),
            ]);

            return ['status' => true, 'url' => $session->url];
        } catch (Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return ['status' => false, 'message' => Lang::get('subscription_error')];
        }
    }

    /**
     * Cancel the user's current subscription plan.
     */
    public function cancelSubscription($user)
    {
        try {
            // Get active subscription
            $subscription = $user->subscription;

            if (!$subscription || !$subscription->isActive()) {
                return ['success' => false, 'message' => Lang::get('subscription_not_found')];
            }

            // Initialize Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Cancel Stripe Subscription
            $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
            $stripeSubscription->cancel();

            // Update Subscription Status in Database
            $subscription->update([
                'status' => 'canceled',
                //'ends_at' => now(), // Mark as ended
            ]);

            return ['success' => true, 'message' => Lang::get('subscription_canceled')];
        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => Lang::get('subscription_cancel_error')];
        }
    }

}