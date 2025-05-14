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
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ParticipantController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:manage_members', ['only' => ['store', 'update', 'destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $query = Participant::with('memberGroups');
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhere('member_id', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('company', 'ilike', "%{$search}%")
                      ->orWhere('location', 'ilike', "%{$search}%")
                      ->orWhere('postal_code', 'ilike', "%{$search}%")
                      ->orWhere('phone', 'ilike', "%{$search}%");
                });
            }
            $members = $query->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'first_name' => $m->first_name,
                    'last_name' => $m->last_name,
                    'member_id' => $m->member_id,
                    'email' => $m->email,
                    'email_cc' => $m->email_cc,
                    'phone' => $m->phone,
                    'gender' => $m->gender,
                    'company' => $m->company,
                    'address' => $m->address,
                    'address_suffix' => $m->address_suffix,
                    'postal_code' => $m->postal_code,
                    'location' => $m->location,
                    'country' => $m->country,
                    'birthday' => $m->birthday ? Carbon::parse($m->birthday)->toDateString() : '',
                    'email_status' => $m->email_status ?? 'OK',
                    'public_registration' => (bool) $m->public_registration,
                    'archived' => (bool) $m->archived,
                    'created_at' => $m->created_at?->toDateTimeString(),
                    'member_groups' => $m->memberGroups->map(function ($g) {
                        return [
                            'id' => $g->id,
                            'name' => $g->name,
                        ];
                    })->toArray(),
                ];
            })->toArray();

            if ($request->wantsJson() || $request->isXmlHttpRequest()) {
                return response()->json($members);
            }
            return Inertia::render('Members/Index', ['members' => $members]);
        } catch (\Exception $e) {
            Log::error('Failed to load members: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load members'], 500);
        }
    }

    public function store(StoreParticipantRequest $request)
    {
        $participant = Participant::create($request->validated());
        if ($request->has('groups')) {
            $groupIds = MemberGroup::whereIn('name', $request->input('groups'))->pluck('id');
            $participant->memberGroups()->sync($groupIds);
        }
        return response()->json(['message' => 'Participant created', 'participant' => $participant], 201);
    }

    public function show(Participant $participant)
    {
        if (!$participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }
        $participant->load('memberGroups');
        return response()->json([
            'id' => $participant->id,
            'gender' => $participant->gender ?? '',
            'first_name' => $participant->first_name ?? '',
            'last_name' => $participant->last_name ?? '',
            'company' => $participant->company ?? '',
            'address' => $participant->address ?? '',
            'address_suffix' => $participant->address_suffix ?? '',
            'postal_code' => $participant->postal_code ?? '',
            'location' => $participant->location ?? '',
            'country' => $participant->country ?? '',
            'birthday' => $participant->birthday ? Carbon::parse($participant->birthday)->toDateString() : '',
            'member_id' => $participant->member_id ?? '',
            'email' => $participant->email ?? '',
            'email_cc' => $participant->email_cc ?? '',
            'phone' => $participant->phone ?? '',
            'public_registration' => (bool) $participant->public_registration,
            'archived' => (bool) $participant->archived,
            'email_status' => $participant->email_status ?? 'OK',
            'created_at' => $participant->created_at?->toDateTimeString(),
            'member_groups' => $participant->memberGroups->map(function ($g) {
                return ['id' => $g->id, 'name' => $g->name];
            })->toArray(),
        ]);
    }

    public function update(UpdateParticipantRequest $request, Participant $participant)
    {
        if (!$participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }
        $data = array_filter($request->validated(), fn($value) => !is_null($value));
        $participant->update($data);

        if ($request->has('groups')) {
            $groupNames = array_filter($request->input('groups'), fn($name) => !empty(trim($name)));
            if (!empty($groupNames)) {
                $groupIds = collect($groupNames)->map(function ($name) {
                    return MemberGroup::firstOrCreate(['name' => trim($name)])->id;
                })->toArray();
                $participant->memberGroups()->sync($groupIds);
            } else {
                $participant->memberGroups()->detach();
            }
        }

        return response()->json(['message' => 'Participant updated', 'participant' => $participant->load('memberGroups')]);
    }

    public function destroy(Participant $participant)
    {
        if (!$participant->exists) {
            return response()->json(['message' => 'Participant not found'], 404);
        }
        $participant->memberGroups()->detach();
        $participant->delete();
        return response()->json(['message' => 'Participant deleted'], 204);
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'mimes:csv,xlsx'],
            ]);

            $path = $request->file('file')->store('imports', 'local');
            $fullPath = Storage::disk('local')->path($path);

            Excel::import(new ParticipantsImport, $fullPath);

            Storage::disk('local')->delete($path);

            return response()->json(['message' => 'Import successful']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->errors()['import'][0] ?? 'Invalid file format'], 422);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 422);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new ParticipantsExport, 'participants.xlsx');
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed: ' . $e->getMessage()], 422);
        }
    }
}