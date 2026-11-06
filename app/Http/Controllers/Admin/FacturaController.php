<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\VentaBorrador;
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
        $cliente = Cliente::orderBy('nombre_razon_social')->get();
        $producto = Producto::where(function ($query) {
            $query->where('state', 1)
                ->orWhere('state', '1')
                ->orWhere('state', 'activo')
                ->orWhere('state', true);
        })->orderBy('producto')->get();

        $borrador = VentaBorrador::firstOrCreate(
            [
                'id_usuario' => auth()->id(),
            ],
            [
                'id_cliente' => null,
            ]
        );

        $borrador->load('detalles.producto');

        return view('admin.facturas.create', compact('cliente', 'producto', 'borrador'));
    }

    public function agregarProducto(Request $request)
    {
        // 1. Validar los datos recibidos
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        // 2. Buscar el producto y verificar que esté activo
        $producto = Producto::where(function ($query) {
            $query->where('state', 1)
                ->orWhere('state', '1')
                ->orWhere('state', 'activo')
                ->orWhere('state', true);
        })->findOrFail($request->id_producto);

        // 3. Obtener o crear el borrador del usuario
        $borrador = VentaBorrador::firstOrCreate(
            [
                'id_usuario' => auth()->id(),
            ],
            [
                'id_cliente' => null,
            ]
        );

        // 4. Buscar el producto dentro del borrador
        $detalle = $borrador->detalles()->firstOrNew([
            'id_producto' => $producto->id,
        ]);

        // 5. Calcular la nueva cantidad acumulada
        $cantidadActual = $detalle->exists
            ? $detalle->cantidad
            : 0;

        $cantidadNueva = $cantidadActual + $request->cantidad;

        // 6. Verificar que no supere el stock disponible
        if ($cantidadNueva > $producto->stock) {
            return back()
                ->withErrors([
                    'cantidad' => "Stock insuficiente. Disponible: {$producto->stock} unidades.",
                ])
                ->withInput();
        }

        // 7. Asignar y guardar la cantidad
        $detalle->cantidad = $cantidadNueva;
        $detalle->save();

        // 8. Redireccionar con mensaje de éxito
        return redirect()
            ->route('facturas.create')
            ->with('success', 'Producto agregado al borrador correctamente.');
    }
}
