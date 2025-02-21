<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Models\EventResponse;
use App\Mail\EventInviteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $event;

    /**
     * Create a new job instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing event reminder for event ID: " . $this->event->id);

        // Fetch users there does not have response 
        User::where('id', '!=', $this->event->user_id)
            ->where('role', 'user')
            ->where('is_active', 1)
            ->whereDoesntHave('eventResponses', function($query) {
                $query->where('event_id', $this->event->id);
            })
            ->chunk(500, function ($users) {
                foreach ($users as $user) {
                    // Send the email notification
                    Mail::to($user)->queue(new EventInviteMail($this->event, $user));
                }
            });

        Log::info("Reminder emails sent successfully for event ID: " . $this->event->id);
    }
}
