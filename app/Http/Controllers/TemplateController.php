<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->get();
        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'msg91_template_id' => 'required|string|unique:templates,msg91_template_id',
            'content' => 'required|string',
            'variables' => 'required|array',
            'variables.*' => 'string',
            'status' => 'required|boolean'
        ]);

        $template = Template::create($validated);

        return response()->json($template);
    }

    public function show(Template $template)
    {
        return response()->json($template);
    }

    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'msg91_template_id' => 'required|string|unique:templates,msg91_template_id,' . $template->id,
            'content' => 'required|string',
            'variables' => 'required|array',
            'variables.*' => 'string',
            'status' => 'required|boolean'
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    public function toggleStatus(Template $template)
    {
        $template->update(['status' => !$template->status]);
        return response()->json($template);
    }
}
