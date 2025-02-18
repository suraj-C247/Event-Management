<?php

namespace App\Http\Services;

use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Illuminate\Support\Facades\Log;
use Exception;

class PlanService
{
    /**
     * Get the list of plans for the authenticated user.
     */
    public function listPlans()
    {
        try {

            // Return paginated plans
            return Plan::latest() 
                ->paginate(config('global.pagination.per_page'));
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error fetching plans: ' . $e->getMessage());
            throw new Exception('An error occurred while fetching plans.');
        }
    }

    /**
     * Create a new plan.
     */
    public function createPlan($request)
    {
        try {
            
            Stripe::setApiKey(config('services.stripe.secret'));

            $product = Product::create(['name' => $request->name]);
            $price = Price::create([
                'unit_amount' => $request->price * 100,
                'currency' => config('services.stripe.currency'),
                'recurring' => ['interval' => $request->type],
                'product' => $product->id,
            ]);

            $plan = Plan::create([
                'name' => $request->name,
                'price' => $request->price,
                'duration' => 1,
                'type' => $request->type,
                'description' => $request->description,
                'max_events' => $request->max_events,
                'stripe_price_id' => $price->id,
            ]);

            return $plan;  
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error creating plan: ' . $e->getMessage());
            throw new Exception('An error occurred while creating the plan.');
        }
    }

}