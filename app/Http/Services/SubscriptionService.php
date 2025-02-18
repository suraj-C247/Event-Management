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
                    'plan_id' => $plan->id 
                ],
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel'),
            ]);

            Log::info('Stripe Checkout Session: ' . $session);

            return ['status' => true, 'url' => $session->url];
        } catch (Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return ['status' => false, 'message' => Lang::get('subscription_error')];
        }
    }

    public function handleSuccess($sessionId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session) {
                return ['status' => false, 'message' => Lang::get('subscription_error')];
            }

            // Retrieve plan_id from Stripe metadata
            $planId = $session->metadata->plan_id;
            $plan = Plan::find($planId);
            if (!$plan) {
                return ['status' => false, 'message' => Lang::get('plan_not_found')];
            }

            // Retrieve subscription details from Stripe
            $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);

            $userId = Auth::id();
            $existingSubscription = Subscription::where('user_id', $userId)->first();

            if ($existingSubscription) {
                // Update current subscription instead of creating a new one
                $existingSubscription->update([
                    'user_id' => $userId,
                    'plan_name' => $plan->name,
                    'plan_price' => $plan->price,
                    'plan_type' => $plan->type,
                    'plan_duration' => $plan->duration,
                    'max_events' => $plan->max_events,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($this->getPlanDuration($plan->type)),
                    'stripe_session_id' => $session->id,
                    'stripe_subscription_id' => $session->subscription,
                    'status' => $stripeSubscription->status,
                ]);
    
                return ['status' => true, 'message' => Lang::get('subscription_updated')];
            } else {

                // Create a new subscription if none exists
                Subscription::create([
                    'user_id' => Auth::id(),    
                    'plan_name' => $plan->name,
                    'plan_price' => $plan->price,
                    'plan_type' => $plan->type,
                    'plan_duration' => $plan->duration,
                    'max_events' => $plan->max_events,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($this->getPlanDuration($plan->type)),
                    'stripe_session_id' => $session->id,
                    'stripe_subscription_id' => $session->subscription,
                    'status' => $stripeSubscription->status,
                ]);
            }

            SubscriptionHistory::create([
                'user_id' => Auth::id(),    
                'plan_name' => $plan->name,
                'plan_price' => $plan->price,
                'plan_type' => $plan->type,
                'plan_duration' => $plan->duration,
                'max_events' => $plan->max_events,
                'starts_at' => now(),
                'ends_at' => now()->addDays($this->getPlanDuration($plan->type)),
                'stripe_session_id' => $session->id,
                'stripe_subscription_id' => $session->subscription,
                'status' => $stripeSubscription->status,
            ]);

            return ['status' => true, 'message' => Lang::get('subscription_success')];    
        } catch (Exception $e) {
            Log::error('Subscription Success Error: ' . $e->getMessage());
            return ['status' => false, 'message' => Lang::get('subscription_error')];
        }
    }

    private function getPlanDuration($planType)
    {
        return match ($planType) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 1
        };
    }

}