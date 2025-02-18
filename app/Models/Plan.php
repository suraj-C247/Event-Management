<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'type', 'duration', 'description', 'max_events', 'stripe_price_id', 'is_active'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
