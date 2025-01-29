<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Display a listing of events
    public function index()
    {
        $events = Event::all();
        return response()->json($events);
    }

    // Store a newly created event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'user_id' => 'required|integer',
        ]);

        $event = Event::create($validated);
        return response()->json($event, 201);
    }

    // Show the specified event
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    // Update the specified event
    public function update(Request $request, $id)
    {
        try {
            // Validate only the provided fields
            $validated = $request->validate([
                'event_name' => 'nullable|string|max:255',
                'event_type' => 'nullable|string|max:255',
                'event_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
                'organizer' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|string|max:50',
                'user_id' => 'nullable|integer',
            ]);
    
            // Find the event or throw a 404 error
            $event = Event::findOrFail($id);
    
            // Update only the fields that were provided
            $event->update($validated);
    
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully.',
                'data' => $event
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the event.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    // Remove the specified event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

        // Fetch events by user ID
        public function fetchByUserId($user_id)
        {
            $events = Event::where('user_id', $user_id)->get();
    
            if ($events->isEmpty()) {
                return response()->json(['message' => 'No events found for the given user ID'], 404);
            }
    
            return response()->json($events);
        }
        
}
