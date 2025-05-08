<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\MemberGroup;
use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;
use App\Exports\ParticipantsExport;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%$search%")
                  ->orWhere('last_name', 'ilike', "%$search%")
                  ->orWhere('member_id', 'ilike', "%$search%")
                  ->orWhere('email', 'ilike', "%$search%")
                  ->orWhere('company', 'ilike', "%$search%")
                  ->orWhere('location', 'ilike', "%$search%")
                  ->orWhere('postal_code', 'ilike', "%$search%")
                  ->orWhere('phone', 'ilike', "%$search%")
                  ;
            });
        }
        return $query->with('memberGroups')->paginate(20);
    }

    public function store(StoreParticipantRequest $request)
    {
        $participant = Participant::create($request->validated());
        if ($request->has('groups')) {
            $groupIds = MemberGroup::whereIn('name', $request->input('groups'))->pluck('id');
            $participant->memberGroups()->sync($groupIds);
        }
        return $participant->load('memberGroups');
    }

    public function show(Participant $participant)
    {
        return $participant->load('memberGroups');
    }

    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        $participant->update($request->validated());
        if ($request->has('groups')) {
            $groupIds = MemberGroup::whereIn('name', $request->input('groups'))->pluck('id');
            $participant->memberGroups()->sync($groupIds);
        }
        return $participant->load('memberGroups');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
        return response()->noContent();
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);
        Excel::import(new ParticipantsImport, $request->file('file'));
        return response()->json(['message' => 'Import successful']);
    }

    public function export(Request $request)
    {
        return Excel::download(new ParticipantsExport, 'participants.xlsx');
    }
} 