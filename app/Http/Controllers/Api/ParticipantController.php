<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participants = Participant::latest()->paginate(15);

        return ParticipantResource::collection($participants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipantRequest $request)
    {
        $participant = Participant::create($request->validated());

        return new ParticipantResource($participant);
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant)
    {
        return new ParticipantResource($participant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        $participant->update($request->validated());

        return new ParticipantResource($participant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant)
    {
        $participant->delete();

        return response()->json(['message' => 'Participant deleted']);
    }
}
