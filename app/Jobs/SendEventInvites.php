<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Mail\EventInviteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventInvites implements ShouldQueue
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
        Log::info("Processing event invites for event ID: " . $this->event->id);

        // Fetch users in chunks to prevent memory issues 
        User::where('id', '!=', $this->event->user_id)
            ->where('role', 'user')
            ->where('is_active', 1)
            ->chunk(500, function ($users) {
                foreach ($users as $user) {
                    // Send the email notification
                    Mail::to($user)->queue(new EventInviteMail($this->event, $user));
                }
            });

        Log::info("Event invites sent successfully for event ID: " . $this->event->id);
    }
}
