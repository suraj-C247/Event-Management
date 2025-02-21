<?php

namespace App\Http\Services;

use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Stripe\Subscription as StripeSubscription;
use Illuminate\Support\Facades\DB;  
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Constructor to set the Stripe API key
     */
    public function __construct()
    {
        // Initialize Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
    }
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
            $user = Auth::user();
            $plan = Plan::findOrFail($planId);

            // Check if user already has a Stripe customer ID
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                // Save the Stripe customer ID in the users table
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                //'customer_email' => Auth::user()->email,
                'customer' => $user->stripe_customer_id,
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
     * Handle the success of the checkout session.
     */
    public function handleSuccess($sessionId)
    {
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
    
            // Retrieve plan_id from Stripe metadata
            $planId = $session->metadata->plan_id ?? null;
            $plan = Plan::find($planId);

            return $plan;
            
        } catch (Exception $e) {
            Log::error('Subscription Success Error: ' . $e->getMessage());
            throw new Exception('An error occurred while fetching subscription.');
        }
    }

    /**
     * Cancel the user's current subscription plan.
     */
    public function cancelSubscription($user)
    {
        try {
            // Get active subscription
            $subscription = $user->latestSubscription;

            if (!$subscription || !$subscription->isActive()) {
                return ['success' => false, 'message' => Lang::get('subscription_not_found')];
            }

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

     /**
     * Upgrade or Downgrade Subscription with Immediate Proration
     */
    public function changeSubscription($newPlanId)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $currentSubscription = $user->latestSubscription;

            if (!$user->stripe_customer_id) {
                throw new Exception(__('Stripe customer ID is missing.'));
            }

            if (!$currentSubscription || !$currentSubscription->isActive()) {
                throw new Exception(__('No active subscription found.'));
            }

            $newPlan = Plan::find($newPlanId);
            if (!$newPlan) {
                throw new Exception(__('Invalid plan selected.'));
            }

            $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
            // Check if a default payment method exists
            if (empty($customer->invoice_settings->default_payment_method)) {
                throw new Exception(__('No payment method found. Please add a payment method first.'));
            }

            // Cancel current subscription immediately
            $stripeSubscription = StripeSubscription::retrieve($currentSubscription->stripe_subscription_id);
            $stripeSubscription->cancel();

            // Create new subscription with proration
            $newStripeSubscription = StripeSubscription::create([
                'customer' => $user->stripe_customer_id,
                'items' => [[ 'price' => $newPlan->stripe_price_id ]],
                'proration_behavior' => 'create_prorations', // Apply proration credit
            ]);

            // Update user's subscription in the database
            $currentSubscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);

            // Create new subscription in the database
            Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $newPlan->name,
                'plan_price' => $newPlan->price,
                'plan_type' => $newPlan->type,
                'plan_duration' => $newPlan->duration,
                'max_events' => $newPlan->max_events,
                'starts_at' => now(),
                'ends_at' => now()->addDays(getPlanDuration($plan->type)),
                'stripe_price_id' => $newPlan->stripe_price_id,
                'stripe_subscription_id' => $newStripeSubscription->id,
                'status' => 'active',
            ]);

            DB::commit();

            return ['success' => Lang::get('subscription_updated')];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Subscription Change Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

}