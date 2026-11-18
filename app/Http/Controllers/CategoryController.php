<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * Altas, edición y borrado de las categorías del hogar activo.
 */
class CategoryController extends Controller
{
    /**
     * Lista las categorías del hogar con su cantidad de movimientos y el rol del usuario.
     */
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

    /**
     * Crea una categoría en el hogar activo; requiere permiso de dueño (un viewer no puede).
     */
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

    /**
     * Edita una categoría del hogar; requiere dueño y no permite cambiar el tipo si ya tiene movimientos.
     */
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
                'type' => 'No podés cambiar el tipo de una categoría con movimientos. Creá otra categoría con el tipo nuevo.',
            ]);
        }

        $category->update($validated);

        return redirect()->back();
    }

    /**
     * Borra la categoría solo si está vacía; requiere permiso de dueño y que no tenga movimientos.
     */
    public function destroy(Category $category)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);
        abort_unless($category->household_id === $household->id, 404);

        if ($category->transactions()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'No podés eliminar una categoría que tiene movimientos. Cambiá esos movimientos a otra categoría y volvé a intentar.',
            ]);
        }

        $category->delete();

        return redirect()->back();
    }
}
