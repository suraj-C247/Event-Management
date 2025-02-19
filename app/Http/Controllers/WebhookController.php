<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Services\WebhookService;
use Stripe\Stripe;
use Stripe\Webhook;
use Exception;

class WebhookController extends Controller
{
    protected $webhookService;

    /**
     * Inject service in constructor
     */
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the checkout.session.completed webhook
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $endpointSecret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid webhook payload.');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Pass the verified event to the service
        return $this->webhookService->handleStripeEvent($event);
    }
}
