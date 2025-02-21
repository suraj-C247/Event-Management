<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Stripe\Subscription as StripeSubscription;
use Illuminate\Support\Facades\Lang;

class WebhookService
{   
    /**
     * Handle Stripe events
     */
    public function handleStripeEvent($event)
    {
        Log::info('Processing Stripe Webhook:', ['event' => $event]);

        if ($event->type === 'checkout.session.completed') {
            return $this->handleCheckoutCompleted($event->data->object);
        }

        if ($event->type === 'invoice.payment_succeeded') {
            // Only process if it's a renewal (ignore the first invoice)
            if (!$event->data['object']['billing_reason'] || $event->data['object']['billing_reason'] !== 'subscription_create') {
                return $this->handleInvoicePaymentSucceeded($event->data->object);
            }
        }

        if ($event->type === 'customer.subscription.deleted') {
            return $this->handleSubscriptionCanceled($event->data->object);
        }

        return response()->json(['message' => Lang::get('webhook_processed')], 200);
    }

    /**
     * Handle Checkout Completed
     */
    private function handleCheckoutCompleted($session)
    {   
        // Retrieve user_id from Stripe metadata
        $userId = $session->metadata->user_id;
        $user = User::find($userId);
        if (!$user) {
            Log::error('User not found with id: ' . $userId);
            return response()->json(['error' => Lang::get('user_not_found')], 404);
        }

        // Get Plan ID from metadata
        $planId = $session->metadata->plan_id;
        $plan = Plan::find($planId);
        if (!$plan) {
            Log::error('Plan not found with ID: ' . $planId);
            return response()->json(['error' => Lang::get('plan_not_found')], 404);
        }

        // Retrieve Subscription details
        $stripeSubscription = StripeSubscription::retrieve($session->subscription);

        // Attach payment method to customer if available
        if (!empty($session->payment_intent)) {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
            if (!empty($paymentIntent->payment_method)) {
                \Stripe\Customer::update($user->stripe_customer_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentIntent->payment_method
                    ]
                ]);
            }
        }

        // save the subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan_name' => $plan->name,
            'plan_price' => $plan->price,
            'plan_type' => $plan->type,
            'plan_duration' => $plan->duration,
            'max_events' => $plan->max_events,
            'starts_at' => now(),
            'ends_at' => now()->addDays(getPlanDuration($plan->type)),
            'stripe_price_id' => $plan->stripe_price_id,
            'stripe_subscription_id' => $session->subscription,
            'status' => 'active',
        ]);

        return response()->json(['message' => Lang::get('subscription_success')], 200);
    }

    /**
     * Handle Invoice Payment Succeeded
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        $stripeSubscriptionId = $invoice->subscription;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->latest('created_at')->first();
        if (!$subscription) {
            Log::error('Subscription not found for Stripe Subscription ID: ' . $stripeSubscriptionId);
            return response()->json(['error' => Lang::get('subscription_not_found')], 404);
        }

        // Create a new subscription history
        Subscription::create([
            'user_id' => $subscription->user_id,
            'plan_name' => $subscription->plan_name,
            'plan_price' => $subscription->plan_price,
            'plan_type' => $subscription->plan_type,
            'plan_duration' => $subscription->plan_duration,
            'max_events' => $subscription->max_events,
            'starts_at' => $subscription->ends_at,
            'ends_at' => now()->parse($subscription->ends_at)->addDays(getPlanDuration($subscription->plan_type)),
            'stripe_price_id' => $subscription->stripe_price_id,
            'stripe_subscription_id' => $invoice->subscription,
            'status' => 'active',
        ]);

        return response()->json(['message' => Lang::get('subscription_renewed')], 200);
    }

    /**
     * Handle Subscription Canceled
     */
    private function handleSubscriptionCanceled($subscriptionData)
    {
        $stripeSubscriptionId = $subscriptionData->id;

        // Find the subscription in our database
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->latest('created_at')->first();
        if (!$subscription) {
            Log::error('Subscription not found for Stripe Subscription ID: ' . $stripeSubscriptionId);
            return response()->json(['error' => Lang::get('subscription_not_found')], 404);
        }

        // Update the subscription status to 'canceled' and set end date to now
        $subscription->update([
            'status' => 'canceled',
            //'ends_at' => now(),
        ]);

        return response()->json(['message' => Lang::get('subscription_canceled')], 200);
    }

}