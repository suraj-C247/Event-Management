<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Event;
use App\Models\EventResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventInviteMail;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendEventReminders;

class SendEventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $events = Event::where('event_date', '=', now()->addHour())->get();
        $events = Event::where('event_date', '=', now()->addMinutes(5))->get();
        Log::info('Events Data: ' . json_encode($events));
        foreach ($events as $event) {
            // $pendingUsers = EventResponse::where('event_id', $event->id)->where('status', 'pending')->pluck('user_id');
            // $users = User::whereIn('id', $pendingUsers)->get();
            // Log::info('Users Data: ' . json_encode($users));
            // foreach ($users as $user) {
            //     // Send reminder email to each user
            //     Mail::to($user)->send(new EventInviteMail($event, $user));
            // }
            SendEventReminders::dispatch($event);
        }

        $this->info('Event reminders sent successfully.');
    }
}
