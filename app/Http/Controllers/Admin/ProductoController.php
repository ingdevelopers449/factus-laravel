<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $consulta = Producto::with('categoria');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $consulta->where(function ($query) use ($buscar) {
                $query->where('producto', 'like', '&'.$buscar.'%')
                    ->orWhere('producto', 'like', '%'.$buscar.'%');
            });
        }

        $productos = $consulta
            ->orderBy('producto')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::orderBy('categoria')->get();

        return view('admin.producto.index', compact(
            'productos',
            'categorias',
        ));

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
        $datos = $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'codigo_barras' => 'nullable|string|max:100',
            'producto' => 'required|string|max:200',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'state' => 'required|boolean',
            'iva' => 'required|numeric|min:0|max:100',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        Producto::create($datos);

        return redirect()
            ->route('admin.producto.index')
            ->with('success', 'Producto creado correctamente.');
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
        $producto = Producto::findOrFail($id);

        $datos = $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'codigo_barras' => 'nullable|string|max:100',
            'producto' => 'required|string|max:200',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'state' => 'required|boolean',
            'iva' => 'required|numeric|min:0|max:100',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $producto->update($datos);

        return redirect()
            ->route('admin.producto.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);

        $producto->delete();

        return redirect()
            ->route('admin.producto.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
