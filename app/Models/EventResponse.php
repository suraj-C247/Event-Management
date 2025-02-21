<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventResponse extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'status'];

    /**
     * Get event data.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get user data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
