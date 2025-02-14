<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use App\Models\EventResponse;
use App\Jobs\SendEventInvites;
use App\Jobs\SendEventReminders;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class EventService
{
    /**
     * Get the list of events for the authenticated user.
     */
    public function listEvents()
    {
        try {
            $user = Auth::user(); // Get the authenticated user

            // Return paginated events with user details
            return Event::select('id', 'title', 'event_date', 'venue', 'image', 'user_id')
                ->with(['user:id,name,email,role'])
                ->when($user->role !== 'admin', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->latest() 
                ->paginate(config('global.pagination.per_page'));
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error fetching events: ' . $e->getMessage());
            throw new Exception('An error occurred while fetching events.');
        }
    }

    /**
     * Create a new event.
     */
    public function createEvent($request)
    {
        try {
            // Decode the base64 encoded image for the event
            $croppedImage = $request->input('cropped_image');
            $fileName = 'event_' . time() . '.jpg'; 
            $filePath = storage_path('app/' . config('global.event.image_path') . $fileName);

            // Clean and decode base64 image string
            $croppedImage = str_replace('data:image/jpeg;base64,', '', $croppedImage);
            $croppedImage = str_replace(' ', '+', $croppedImage);
            $imageData = base64_decode($croppedImage);

            // Save image to storage
            if (file_put_contents($filePath, $imageData) === false) {
                throw new Exception('Error saving image to storage.');
            }

            // Create the event record in the database
            $event = Event::create([
                'user_id' => Auth::id(), 
                'event_date' => $request->event_date,
                'title' => $request->title,
                'description' => $request->description,
                'image' => $fileName,
                'venue' => $request->venue
            ]);

            //Dispatch job to send event invites after the delay
            SendEventInvites::dispatch($event)->delay(now()->addMinutes(config('global.job.invite_delay')));

            // Dispatch reminder job before the event
            $sendAt = Carbon::parse($event->event_date)->subMinutes(config('global.job.reminder_delay'));
            SendEventReminders::dispatch($event)->delay($sendAt);

            return $event;
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error creating event: ' . $e->getMessage());
            throw new Exception('An error occurred while creating the event.');
        }
    }

    /**
     * Save the response of a user to an event (accepted or rejected).
     */
    public function saveEventResponse($event, $user, $status)
    {
        try {
            
            // Check if the status is valid
            if (!in_array($status, [config('global.event.status.accepted'), config('global.event.status.rejected')])) {
                throw new Exception('Invalid status provided.');
            }

             // Check if the user has already responded
            $response = EventResponse::where('event_id', $event->id)
                             ->where('user_id', $user->id)
                             ->first();

            // If already have response, return null
            if ($response) {
                return null;
            }

            // Create a new response if not already exists
            $response = EventResponse::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => $status
            ]);

            return $response;
        } catch (Exception $e) {
            // Log the exception and rethrow it
            Log::error('Error saving event response: ' . $e->getMessage());
            throw new Exception('An error occurred while saving the event response.');
        }
    }
}