<?php

namespace App\Http\Controllers;

use App\Models\MemberGroup;
use App\Models\Participant;
use Illuminate\Http\Request;

class MemberGroupController extends Controller
{
    public function index()
    {
        return MemberGroup::withCount('participants')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|unique:member_groups,name']);
        return MemberGroup::create($data);
    }

    public function update(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['name' => 'required|string|unique:member_groups,name,' . $memberGroup->id]);
        $memberGroup->update($data);
        return $memberGroup;
    }

    public function destroy(MemberGroup $memberGroup)
    {
        $memberGroup->delete();
        return response()->noContent();
    }

    public function assignParticipant(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id']);
        $memberGroup->participants()->attach($data['participant_id']);
        return response()->noContent();
    }

    public function removeParticipant(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id']);
        $memberGroup->participants()->detach($data['participant_id']);
        return response()->noContent();
    }
} 