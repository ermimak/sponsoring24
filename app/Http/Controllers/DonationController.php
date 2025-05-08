<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::query();
        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }
        if ($request->has('participant_id')) {
            $query->where('participant_id', $request->input('participant_id'));
        }
        return $query->with(['project', 'participant'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'participant_id' => 'nullable|exists:participants,id',
            'supporter_id' => 'nullable|integer',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'billing_date' => 'nullable|date',
            'status' => 'string',
            'payment_method' => 'nullable|string',
            'currency' => 'string',
        ]);
        return Donation::create($data);
    }

    public function show(Donation $donation)
    {
        return $donation->load(['project', 'participant']);
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'amount' => 'numeric',
            'type' => 'string',
            'billing_date' => 'nullable|date',
            'status' => 'string',
            'payment_method' => 'nullable|string',
            'currency' => 'string',
        ]);
        $donation->update($data);
        return $donation;
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return response()->noContent();
    }
} 