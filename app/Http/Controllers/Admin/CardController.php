<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::with(['organization', 'group'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cards = $query->paginate(20)->withQueryString();

        return view('admin.cards.index', compact('cards'));
    }

    public function create()
    {
        $organizations = Organization::where('status', 'active')->orderBy('name')->get();
        $groups = Group::where('status', 'active')->orderBy('name')->get();

        return view('admin.cards.create', compact('organizations', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'organization_id' => 'nullable|exists:organizations,id',
            'group_id'        => 'nullable|exists:groups,id',
        ]);

        $card = Card::create(array_merge($validated, [
            'config'     => null,
            'status'     => 'draft',
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('admin.cards.builder', $card)
            ->with('success', 'Carte créée.');
    }

    public function builder(Card $card)
    {
        $config = $card->config ?? $card->defaultConfig();

        return view('admin.cards.builder', compact('card', 'config'));
    }

    public function saveConfig(Request $request, Card $card)
    {
        $request->validate([
            'config' => 'required|array',
        ]);

        $card->update(['config' => $request->config]);

        return response()->json(['success' => true, 'saved_at' => now()->toISOString()]);
    }

    public function preview(Request $request, Card $card)
    {
        $config = $card->config ?? $card->defaultConfig();

        if ($request->filled('employee_id')) {
            $employee = Employee::findOrFail($request->employee_id);
        } else {
            $employee = new Employee([
                'first_name'   => 'Alice',
                'last_name'    => 'Martin',
                'job_title'    => 'Responsable Pédagogique',
                'department'   => 'Direction',
                'email'        => 'alice.martin@mmi-e.fr',
                'phone'        => '+33 6 00 00 00 01',
                'linkedin_url' => 'https://linkedin.com/in/alice-martin',
                'calendly_url' => null,
                'slug'         => 'alice-martin',
                'qr_token'     => 'demo-token',
                'is_active'    => true,
            ]);
        }

        return view('admin.cards.preview', compact('card', 'config', 'employee'));
    }

    public function publish(Card $card)
    {
        $card->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Carte publiée.');
    }

    public function templates(Request $request)
    {
        $query = CardTemplate::latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $templates = $query->get();
        $categories = CardTemplate::distinct()->pluck('category')->sort()->values();

        if ($request->wantsJson()) {
            return response()->json(['data' => $templates]);
        }

        return view('admin.cards.templates', compact('templates', 'categories'));
    }

    public function fromTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:card_templates,id',
            'name'        => 'nullable|string|max:255',
        ]);

        $template = CardTemplate::findOrFail($request->template_id);

        $card = Card::create([
            'name'        => $request->name ?: 'Copie de ' . $template->name,
            'template_id' => $template->id,
            'config'      => $template->config,
            'status'      => 'draft',
            'created_by'  => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'card_id'  => $card->id,
                'redirect' => route('admin.cards.builder', $card),
            ], 201);
        }

        return redirect()->route('admin.cards.builder', $card);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'organization_id' => 'nullable|exists:organizations,id',
            'group_id'        => 'nullable|exists:groups,id',
            'name'            => 'nullable|string|max:255',
        ]);

        $config = (new Card())->defaultConfig();

        if ($request->filled('organization_id')) {
            $org = Organization::find($request->organization_id);
            if ($org) {
                $config['canvas']['background']['value'] = $org->primary_color;
            }
        }

        $card = Card::create([
            'name'            => $request->name ?: 'Carte générée',
            'organization_id' => $request->organization_id,
            'group_id'        => $request->group_id,
            'config'          => $config,
            'status'          => 'draft',
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('admin.cards.builder', $card);
    }
}
