<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gemstone;
use App\Models\MetalType;
use App\Models\Occasion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Metal types, gemstones and occasions are three structurally identical
 * lookup tables (name/slug/description/is_active/sort_order) — rather
 * than tripling the same CRUD controller, this one resolves which model
 * to use from the route's own `type` default (see routes/web.php), kept
 * to the 3 explicit URL prefixes B15 asks for (/admin/metal-types,
 * /admin/gemstones, /admin/occasions) — not a generic/open wildcard.
 */
class AttributeController extends Controller
{
    private const TYPES = [
        'metal-types' => ['model' => MetalType::class, 'label' => 'Metal Type', 'plural' => 'Metal Types'],
        'gemstones' => ['model' => Gemstone::class, 'label' => 'Gemstone', 'plural' => 'Gemstones'],
        'occasions' => ['model' => Occasion::class, 'label' => 'Occasion', 'plural' => 'Occasions'],
    ];

    public function index(Request $request): View
    {
        $meta = $this->meta($request);
        $items = $meta['model']::orderBy('sort_order')->get();

        return view('admin.attributes.index', ['items' => $items, 'meta' => $meta]);
    }

    public function create(Request $request): View
    {
        $meta = $this->meta($request);

        return view('admin.attributes.form', ['item' => new $meta['model'], 'meta' => $meta]);
    }

    public function store(Request $request): RedirectResponse
    {
        $meta = $this->meta($request);
        $validated = $this->validated($request, $meta);
        $validated['is_active'] = $request->boolean('is_active', true);

        $meta['model']::create($validated);

        return redirect()->route('admin.'.$meta['type'].'.index')->with('success', $meta['label'].' created.');
    }

    public function edit(Request $request, int $attribute): View
    {
        $meta = $this->meta($request);
        $item = $meta['model']::findOrFail($attribute);

        return view('admin.attributes.form', ['item' => $item, 'meta' => $meta]);
    }

    public function update(Request $request, int $attribute): RedirectResponse
    {
        $meta = $this->meta($request);
        $item = $meta['model']::findOrFail($attribute);

        $validated = $this->validated($request, $meta, $item);
        $validated['is_active'] = $request->boolean('is_active');

        $item->update($validated);

        return redirect()->route('admin.'.$meta['type'].'.index')->with('success', $meta['label'].' updated.');
    }

    public function destroy(Request $request, int $attribute): RedirectResponse
    {
        $meta = $this->meta($request);
        $item = $meta['model']::findOrFail($attribute);

        if ($item->products()->exists()) {
            return back()->with('error', 'This '.$meta['label'].' is still used by products — remove it from those products first.');
        }

        $item->delete();

        return redirect()->route('admin.'.$meta['type'].'.index')->with('success', $meta['label'].' removed.');
    }

    /**
     * @return array{type: string, model: class-string, label: string, plural: string}
     */
    private function meta(Request $request): array
    {
        $type = $request->route('type');

        abort_unless(array_key_exists($type, self::TYPES), 404);

        return self::TYPES[$type] + ['type' => $type];
    }

    /**
     * @param  array{type: string, model: class-string, label: string, plural: string}  $meta
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $meta, $item = null): array
    {
        $table = (new $meta['model'])->getTable();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique($table, 'slug')->ignore($item?->id)],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
