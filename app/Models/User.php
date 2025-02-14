<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all the events for the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Define the relationship with the EventResponse model.
     */
    public function eventResponses(): HasMany
    {
        return $this->hasMany(EventResponse::class, 'user_id');
    }

    // Automatically delete event image when the user is deleted
    protected static function booted()
    {
        static::deleting(function ($user) {

            foreach ($user->events as $event) {
                $imagePath = storage_path('app/public/events/' . $event->image); 

                if ($event->image && file_exists($imagePath)) { 
                    unlink($imagePath); 
                }
            }

            // Delete events manually before deleting the user
            $user->events()->delete();
        });
    }
}
