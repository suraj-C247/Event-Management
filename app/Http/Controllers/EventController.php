<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Http\Services\EventService;
use Illuminate\Support\Facades\Lang;

class EventController extends Controller
{   
    protected $eventService;

    /**
     * Constructor to inject EventService dependency.
     
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Show the list of events for the authenticated user.
     */
    public function index(): View
    {   
        // Get events from EventService
        $events = $this->eventService->listEvents();
        
        return view('events.index', compact('events'));
    }

    /**
     * Show the form to create a new event.
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in the database.
     */
    public function store(EventRequest $request): RedirectResponse
    {

        // Delegate event creation to EventService
        $this->eventService->createEvent($request);

        return redirect()->route('events.index')->with('success', Lang::get('messages.event_created'));
    }

    /**
     * Handle a user's response to an event (accept/reject).
     */
    public function respondToEvent(Event $event, User $user, $status): RedirectResponse
    {
        // Response saving to EventService
        $response = $this->eventService->saveEventResponse($event, $user, $status);

        // If response is null, it means the link has expired
        if ($response === null) {
            return redirect()->route('event.msg')->with('error', Lang::get('messages.event_link_expired'));
        }

        // If response is false, the status is invalid
        if ($response === false) {
            return redirect()->route('event.msg')->with('error', Lang::get('messages.error_occurred'));
        }

        return redirect()->route('event.msg')->with('success', Lang::get('messages.event_response'));
    }

    /**
     * Show a confirmation message after responding to an event.
     */
    public function respondMsg(): View
    {
        return view('events.msg');
    }

}
