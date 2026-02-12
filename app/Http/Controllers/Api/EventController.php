<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $filters = [
            'location' => $request->get('location'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'search' => $request->get('search'),
            'page' => $page,
            'per_page' => $perPage,
        ];

        // Cache the events list for 10 minutes keyed by filters.
        $cacheKey = 'events_index_'.md5(json_encode($filters));

        $paginated = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $perPage) {
            return Event::query()
                ->when($request->get('location'), fn ($q, $location) => $q->where('location', 'like', '%'.$location.'%'))
                ->filterByDate($request->get('from'), $request->get('to'))
                ->searchByTitle($request->get('search'))
                ->with('tickets')
                ->paginate($perPage);
        });

        return $this->successResponse('Events fetched successfully', $paginated);
    }

    public function show(Event $event)
    {
        $event->load('tickets', 'organizer');

        return $this->successResponse('Event details', $event);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $data['user_id'] = $request->user()->id;

        $event = Event::create($data);

        return $this->successResponse('Event created successfully', $event, 201);
    }

    public function update(Request $request, Event $event)
    {
        // Admins can update any event; organizers only their own.
        $user = $request->user();

        if ($user->role === 'organizer' && $event->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only modify your own events.'], 403);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ]);

        $event->update($data);

        return $this->successResponse('Event updated successfully', $event);
    }

    public function destroy(Request $request, Event $event)
    {
        $user = $request->user();

        if ($user->role === 'organizer' && $event->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only delete your own events.'], 403);
        }

        $event->delete();

        return $this->successResponse('Event deleted successfully');
    }
}


