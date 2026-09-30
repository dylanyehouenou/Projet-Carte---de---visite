<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardTemplate;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::with('organization')->withCount('employees');

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $groups = $query->latest()->paginate(20)->withQueryString();
        $organizations = Organization::where('status', 'active')->orderBy('name')->get();

        return view('admin.groups.index', compact('groups', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::where('status', 'active')->orderBy('name')->get();
        $templates = CardTemplate::orderBy('name')->get();

        return view('admin.groups.create', compact('organizations', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id'          => 'required|exists:organizations,id',
            'name'                     => 'required|string|max:255',
            'description'              => 'nullable|string',
            'logo_media_id'            => 'nullable|exists:media,id',
            'primary_color'            => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color'          => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'default_card_template_id' => 'nullable|exists:card_templates,id',
            'status'                   => 'required|in:active,inactive',
        ]);

        Group::create($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Groupe créé avec succès.');
    }

    public function edit(Group $group)
    {
        $organizations = Organization::where('status', 'active')->orderBy('name')->get();
        $templates = CardTemplate::orderBy('name')->get();

        return view('admin.groups.edit', compact('group', 'organizations', 'templates'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'organization_id'          => 'required|exists:organizations,id',
            'name'                     => 'required|string|max:255',
            'description'              => 'nullable|string',
            'logo_media_id'            => 'nullable|exists:media,id',
            'primary_color'            => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color'          => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'default_card_template_id' => 'nullable|exists:card_templates,id',
            'status'                   => 'required|in:active,inactive',
        ]);

        $group->update($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Groupe mis à jour.');
    }

    public function destroy(Group $group)
    {
        if ($group->employees()->exists()) {
            return back()->with('error', 'Impossible de supprimer un groupe avec des collaborateurs.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Groupe supprimé.');
    }
}
