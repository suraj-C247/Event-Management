<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionHistory extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'plan_name', 'plan_price', 'plan_type', 'plan_duration', 'max_events', 'starts_at', 'ends_at', 'stripe_subscription_id', 'stripe_session_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
