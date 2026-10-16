<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');

        $categorias = Categoria::when($buscar, function ($query, $buscar) {
            $query->where('categoria', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%");
        })
            ->orderBy('categoria')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'state' => 'required|boolean',
        ]);

        Categoria::create($request->only([
            'categoria',
            'descripcion',
            'state',
        ]));

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'state' => 'required|boolean',
        ]);

        $categoria->update($request->only([
            'categoria',
            'descripcion',
            'state',
        ]));

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
