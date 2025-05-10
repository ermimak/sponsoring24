<?php

namespace App\Http\Controllers;

use App\Models\MemberGroup;
use App\Models\Participant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberGroupController extends Controller
{
    public function index()
    {
        $groups = MemberGroup::withCount('participants')->get();
        if (request()->wantsJson() || request()->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|unique:member_groups,name']);
        MemberGroup::create($data);
        $groups = MemberGroup::withCount('participants')->get();
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }

    public function update(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['name' => 'required|string|unique:member_groups,name,' . $memberGroup->id]);
        $memberGroup->update($data);
        $groups = MemberGroup::withCount('participants')->get();
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }

    public function destroy(MemberGroup $memberGroup)
    {
        $memberGroup->delete();
        $groups = MemberGroup::withCount('participants')->get();
        if (request()->wantsJson() || request()->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }

    public function assignParticipant(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id']);
        $memberGroup->participants()->attach($data['participant_id']);
        $groups = MemberGroup::withCount('participants')->get();
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }

    public function removeParticipant(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate(['participant_id' => 'required|exists:participants,id']);
        $memberGroup->participants()->detach($data['participant_id']);
        $groups = MemberGroup::withCount('participants')->get();
        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json($groups);
        }
        return Inertia::render('Members/Groups', [
            'groups' => $groups
        ]);
    }
} 