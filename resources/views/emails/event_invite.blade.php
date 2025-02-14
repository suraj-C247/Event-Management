<!DOCTYPE html>
<html>
<head>
    <title>Event Invitation</title>
</head>
<body>
    <h2>You're invited to: {{ $event->title }}</h2>
    <p><strong>Date & Time:</strong> {{ $event->event_date }}</p>
    <p><strong>Venue:</strong> {{ $event->venue }}</p>
    <p>{!! $event->description !!}</p>

    <p>Click below to respond:</p>
    <a href="{{ route('event.response', ['event' => $event->id, 'user' => $user->id, 'status' => 'accepted']) }}">Accept</a> | 
    <a href="{{ route('event.response', ['event' => $event->id, 'user' => $user->id, 'status' => 'rejected']) }}">Reject</a>

    <p>Thank you!</p>
</body>
</html>