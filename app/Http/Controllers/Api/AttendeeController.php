<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\canLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class AttendeeController extends Controller
{
        use  canLoadRelationships ;
        private array $relations = ['user'];

        public function __construct()
        {
            $this->middleware('auth:sanctum')->except(['index','show' , 'update']);
            $this->middleware('throttle:api')->only(['store', 'destroy']);

            $this->authorizeResource(Attendee::class, 'attendee');
        }
    /**
     * Display a listing of the resource.
     */public function index(Event $event)
{
    $attendees = $this->loadRelationships($event->attendees()->latest()->getQuery());

    return AttendeeResource::collection($attendees->paginate(10));
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , Event $event)
    {
        $attendee = $this->loadRelationships($event->attendees()->create([
            'user_id' => $request->user()->id,
        ]));
        return new AttendeeResource($attendee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($this->loadRelationships($attendee));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        // $this->authorize('delete-attendee', [$event, $attendee]);
        $attendee->delete();
        return response(status:204);
    }

}
