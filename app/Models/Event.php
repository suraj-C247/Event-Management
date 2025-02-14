<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_date', 'title', 'description', 'image', 'venue'];

    /**
     * Get user data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Define the relationship with the EventResponse model.
     */
    public function responses()
    {
        return $this->hasMany(EventResponse::class);
    }

}
