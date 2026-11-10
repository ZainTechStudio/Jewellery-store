<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category,
            'parents' => Category::whereNull('parent_id')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validated($request, $category);
        $validated['is_active'] = $request->boolean('is_active');

        // Prevent a category becoming its own parent, and prevent making a
        // category a child of one of its own children (a 2-level cycle) —
        // the only kind this schema's actual depth could ever produce.
        if (! empty($validated['parent_id'])) {
            if ((int) $validated['parent_id'] === $category->id) {
                return back()->withErrors(['parent_id' => 'A category cannot be its own parent.'])->withInput();
            }

            if (Category::where('id', $validated['parent_id'])->where('parent_id', $category->id)->exists()) {
                return back()->withErrors(['parent_id' => 'That category is a child of this one — choose a different parent.'])->withInput();
            }
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    /**
     * Soft-deletes. Any products still assigned to this category keep
     * their category_id (restrictOnDelete on products.category_id means
     * a hard delete would be blocked anyway if products exist) — soft
     * delete here just hides it from pickers without breaking that FK.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'This category still has products assigned — reassign them before removing it.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
