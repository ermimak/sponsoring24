<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->query('project_id');

        if (!$projectId) {
            return response()->json(['error' => 'Project ID is required'], 400);
        }

        try {
            $templates = EmailTemplate::where('project_id', $projectId)->get();
            return response()->json(['data' => $templates]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch email templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch email templates'], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'show_logo' => 'boolean',
            'show_header_image' => 'boolean',
            'show_placeholders' => 'boolean',
            'regarding' => 'nullable|string',
            'reply_to' => 'nullable|email',
            'sender_name' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        $template = EmailTemplate::create($data);
        return response()->json($template, 201);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return response()->json($emailTemplate);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'footer' => 'nullable|string',
            'notes' => 'nullable|string',
            'show_logo' => 'boolean',
            'show_header_image' => 'boolean',
            'show_placeholders' => 'boolean',
            'regarding' => 'nullable|string',
            'reply_to' => 'nullable|email',
            'sender_name' => 'nullable|string',
        ]);

        $emailTemplate->update($data);
        return response()->json($emailTemplate);
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return response()->noContent();
    }
}