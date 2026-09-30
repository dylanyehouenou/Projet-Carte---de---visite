<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::withCount('employees');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $organizations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('admin.organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'primary_color'           => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color'         => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color'              => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo_media_id'           => 'nullable|exists:media,id',
            'secondary_logo_media_id' => 'nullable|exists:media,id',
            'website'                 => 'nullable|url|max:255',
            'address'                 => 'nullable|string|max:500',
            'status'                  => 'required|in:active,inactive',
        ]);

        Organization::create($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisation créée avec succès.');
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'primary_color'           => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color'         => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color'              => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo_media_id'           => 'nullable|exists:media,id',
            'secondary_logo_media_id' => 'nullable|exists:media,id',
            'website'                 => 'nullable|url|max:255',
            'address'                 => 'nullable|string|max:500',
            'status'                  => 'required|in:active,inactive',
        ]);

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisation mise à jour.');
    }

    public function destroy(Organization $organization)
    {
        if ($organization->employees()->exists()) {
            return back()->with('error', 'Impossible de supprimer une organisation avec des collaborateurs actifs.');
        }

        $organization->update(['status' => 'inactive']);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisation désactivée.');
    }
}
