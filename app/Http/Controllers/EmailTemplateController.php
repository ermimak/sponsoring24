<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();
        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }
        return $query->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string',
            'name' => 'required|string',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);
        return EmailTemplate::create($data);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return $emailTemplate;
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'type' => 'string',
            'name' => 'string',
            'subject' => 'string',
            'body' => 'string',
        ]);
        $emailTemplate->update($data);
        return $emailTemplate;
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return response()->noContent();
    }
} 