<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el texto de búsqueda
        $buscar = $request->input('buscar');

        // 2. Construir la consulta
        $facturas = Factura::with('cliente', 'detalles')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('consecutivo', 'LIKE', "%{$buscar}%");
            })
            ->paginate(10)
            ->withQueryString();

        // 3. Enviar los datos a la vista
        return view('admin.facturas.index', compact('facturas', 'buscar'));
    }

    public function create()
    {
        $cliente = Cliente::all();
        $producto = Producto::where('state', 1)->get();

        return view('admin.facturas.create', compact('cliente', 'producto'));
    }
}
