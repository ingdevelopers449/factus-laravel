<?php

namespace App\Http\Controllers\Admin;

use App\Models\Factura;
use App\Http\Controllers\Controller;
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
}
