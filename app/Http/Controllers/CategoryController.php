<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $household = $this->currentHousehold();

        return Inertia::render('Categories/Index', [
            'categories' => $household->categories()
                ->withCount('transactions')
                ->orderBy('name')
                ->get(),
            'role' => $this->roleIn($household),
        ]);
    }

    public function store(Request $request)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:gasto,ganancia',
            'color' => 'nullable|string|max:7',
            'budget' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        $household->categories()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Category $category)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);
        abort_unless($category->household_id === $household->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:gasto,ganancia',
            'color' => 'nullable|string|max:7',
            'budget' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        if ($category->type !== $validated['type'] && $category->transactions()->exists()) {
            throw ValidationException::withMessages([
                'type' => 'No podés cambiar el tipo: la categoría ya tiene movimientos.',
            ]);
        }

        $category->update($validated);

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);
        abort_unless($category->household_id === $household->id, 404);

        if ($category->transactions()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'No podés borrar una categoría con movimientos.',
            ]);
        }

        $category->delete();

        return redirect()->back();
    }
}
