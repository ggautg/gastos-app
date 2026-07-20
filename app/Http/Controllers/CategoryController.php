<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $household = auth()->user()->currentHousehold();

        $categories = $household->categories()->orderBy('name')->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'role' => auth()->user()->households()->first()->pivot->role,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeViewerCannotEdit();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:gasto,ganancia',
            'color' => 'nullable|string|max:7',
            'budget' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        auth()->user()->currentHousehold()->categories()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeViewerCannotEdit();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:gasto,ganancia',
            'color' => 'nullable|string|max:7',
            'budget' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        $category->update($validated);

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $this->authorizeViewerCannotEdit();

        $category->delete();

        return redirect()->back();
    }

    private function authorizeViewerCannotEdit(): void
    {
        $role = auth()->user()->households()->first()->pivot->role;

        if ($role === 'viewer') {
            abort(403, 'No tenés permiso para modificar datos en este household.');
        }
    }
}
