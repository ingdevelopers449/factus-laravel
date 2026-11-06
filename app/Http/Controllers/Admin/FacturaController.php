<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Resolucion;
use App\Models\VentaBorrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function eliminarProducto(string $id)
    {
        $borrador = VentaBorrador::where('id_usuario', auth()->id())->first();

        if ($borrador) {
            $borrador->detalles()->where('id_detalle_borrador', $id)->delete();
        }

        return redirect()
            ->route('facturas.create')
            ->with('success', 'Producto retirado de la lista.');
    }

    public function vaciarBorrador()
    {
        $borrador = VentaBorrador::where('id_usuario', auth()->id())->first();

        if ($borrador) {
            $borrador->detalles()->delete();
        }

        return redirect()
            ->route('facturas.create')
            ->with('success', 'Venta cancelada y lista vaciada correctamente.');
    }

    public function store(Request $request)
    {
        // 1. Validar los datos recibidos del formulario de facturación
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'fecha_emision' => 'required|date',
        ], [
            'id_cliente.required' => 'Debes seleccionar un cliente para generar la factura.',
            'id_cliente.exists' => 'El cliente seleccionado no es válido.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
        ]);

        // 2. Obtener el borrador del usuario actual con sus productos
        $borrador = VentaBorrador::with('detalles.producto')
            ->where('id_usuario', auth()->id())
            ->first();

        if (! $borrador || $borrador->detalles->isEmpty()) {
            return back()
                ->withErrors(['general' => 'No hay productos en la lista para facturar. Agrega al menos un producto.'])
                ->withInput();
        }

        // 3. Obtener la resolución DIAN activa
        $resolucion = Resolucion::where('estado', 'activa')->first();

        if (! $resolucion) {
            return back()
                ->withErrors(['general' => 'No existe una Resolución DIAN activa configurada. Configúrala en Parametrización DIAN.'])
                ->withInput();
        }

        // Verificar límite de numeración
        if ($resolucion->contador_actual >= $resolucion->rango_final) {
            return back()
                ->withErrors(['general' => "La resolución DIAN {$resolucion->prefijo} ha alcanzado su límite de numeración autorizado (#{$resolucion->rango_final})."])
                ->withInput();
        }

        // 4. Obtener la empresa asociada (de la resolución o primera empresa registrada)
        $idEmpresa = $resolucion->id_empresa ?? Empresa::first()?->id_empresa ?? 1;

        // 5. Validar disponibilidad de stock de todos los productos en el carrito
        foreach ($borrador->detalles as $detalle) {
            $prod = $detalle->producto;

            if (! $prod) {
                return back()
                    ->withErrors(['general' => 'Uno de los productos en el carrito ya no existe en el catálogo.'])
                    ->withInput();
            }

            if ($detalle->cantidad > $prod->stock) {
                return back()
                    ->withErrors(['general' => "Stock insuficiente para '{$prod->producto}'. Solicitado: {$detalle->cantidad}, Disponible: {$prod->stock}."])
                    ->withInput();
            }
        }

        // 6. Transacción atómica de base de datos
        $factura = DB::transaction(function () use ($request, $borrador, $resolucion, $idEmpresa) {
            $nuevoConsecutivo = $resolucion->contador_actual + 1;

            $subtotal = 0;
            $totalIva = 0;

            foreach ($borrador->detalles as $det) {
                $lineaSubtotal = $det->cantidad * (float) $det->producto->precio_venta;
                $lineaIva = $lineaSubtotal * (((float) ($det->producto->iva ?? 0)) / 100);

                $subtotal += $lineaSubtotal;
                $totalIva += $lineaIva;
            }

            $totalPagar = $subtotal + $totalIva;

            // Crear la Factura
            $nuevaFactura = Factura::create([
                'id_empresa' => $idEmpresa,
                'id_resolucion' => $resolucion->id_resolucion,
                'prefijo_resolucion' => $resolucion->prefijo,
                'consecutivo' => $nuevoConsecutivo,
                'fecha_emision' => $request->fecha_emision.' '.now()->format('H:i:s'),
                'id_cliente' => $request->id_cliente,
                'id_usuario' => auth()->id(),
                'subtotal' => $subtotal,
                'total_iva' => $totalIva,
                'total_pagar' => $totalPagar,
                'porcentaje_comision_aplicado' => 0,
                'cufe' => null,
                'codigo_qr' => null,
                'estado_dian' => 'pendiente',
                'motivo_rechazo' => null,
                'fecha_validacion_dian' => null,
            ]);

            // Guardar detalles y descontar stock de inventario
            foreach ($borrador->detalles as $det) {
                $lineaSubtotal = $det->cantidad * (float) $det->producto->precio_venta;

                DetalleFactura::create([
                    'id_factura' => $nuevaFactura->id,
                    'id_producto' => $det->producto->id,
                    'cantidad' => $det->cantidad,
                    'precio_unitario_venta' => $det->producto->precio_venta,
                    'precio_unitario_costo' => $det->producto->precio_compra ?? 0,
                    'subtotal_linea' => $lineaSubtotal,
                ]);

                // Descontar inventario
                $det->producto->decrement('stock', $det->cantidad);
            }

            // Incrementar contador de la resolución DIAN
            $resolucion->increment('contador_actual');

            // Limpiar los ítems del borrador de venta del cajero
            $borrador->detalles()->delete();

            return $nuevaFactura;
        });

        return redirect()
            ->route('facturas.index')
            ->with('success', "¡Factura {$factura->prefijo_resolucion}-{$factura->consecutivo} generada con éxito!");
    }
}
