<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentabilidadController extends Controller
{
    /**
     * Muestra el reporte financiero de Ventas y Rentabilidad.
     */
    public function index(Request $request)
    {
        // 1. Rango de fechas (por defecto desde el primer día del mes actual hasta hoy)
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        // 2. Totales consolidados de facturación
        $totalesFacturas = Factura::whereDate('fecha_emision', '>=', $fechaInicio)
            ->whereDate('fecha_emision', '<=', $fechaFin)
            ->selectRaw('
                COUNT(*) as total_facturas,
                COALESCE(SUM(subtotal), 0) as total_subtotal,
                COALESCE(SUM(total_iva), 0) as total_iva,
                COALESCE(SUM(total_pagar), 0) as total_pagar
            ')
            ->first();

        $totalFacturas = (int) ($totalesFacturas->total_facturas ?? 0);
        $totalVentasNetas = (float) ($totalesFacturas->total_subtotal ?? 0);
        $totalIva = (float) ($totalesFacturas->total_iva ?? 0);
        $totalVentasConIva = (float) ($totalesFacturas->total_pagar ?? 0);

        // 3. Costo total de ventas (COGS) en el período
        $costoTotal = (float) DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->whereDate('facturas.fecha_emision', '>=', $fechaInicio)
            ->whereDate('facturas.fecha_emision', '<=', $fechaFin)
            ->sum(DB::raw('detalle_factura.cantidad * detalle_factura.precio_unitario_costo'));

        // 4. Ganancia bruta y margen de rentabilidad global
        $gananciaBruta = $totalVentasNetas - $costoTotal;
        $margenPorcentaje = $totalVentasNetas > 0 ? ($gananciaBruta / $totalVentasNetas) * 100 : 0;
        $ticketPromedio = $totalFacturas > 0 ? $totalVentasConIva / $totalFacturas : 0;

        // 5. Ranking de Productos Más Rentables (Top 10)
        $topProductos = DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->join('productos', 'detalle_factura.id_producto', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->whereDate('facturas.fecha_emision', '>=', $fechaInicio)
            ->whereDate('facturas.fecha_emision', '<=', $fechaFin)
            ->select(
                'productos.id',
                'productos.producto',
                'productos.codigo_barras',
                DB::raw("COALESCE(categorias.categoria, 'General') as categoria"),
                DB::raw('SUM(detalle_factura.cantidad) as cantidad_vendida'),
                DB::raw('SUM(detalle_factura.subtotal_linea) as ingresos_ventas'),
                DB::raw('SUM(detalle_factura.cantidad * detalle_factura.precio_unitario_costo) as costo_total'),
                DB::raw('SUM(detalle_factura.subtotal_linea - (detalle_factura.cantidad * detalle_factura.precio_unitario_costo)) as ganancia_bruta'),
                DB::raw('CASE WHEN SUM(detalle_factura.subtotal_linea) > 0 THEN (SUM(detalle_factura.subtotal_linea - (detalle_factura.cantidad * detalle_factura.precio_unitario_costo)) / SUM(detalle_factura.subtotal_linea)) * 100 ELSE 0 END as margen_porcentaje')
            )
            ->groupBy('productos.id', 'productos.producto', 'productos.codigo_barras', 'categorias.categoria')
            ->orderByDesc('ganancia_bruta')
            ->limit(10)
            ->get();

        // 6. Rentabilidad por Categoría (para gráfico de torta/dona)
        $rentabilidadCategorias = DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->join('productos', 'detalle_factura.id_producto', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->whereDate('facturas.fecha_emision', '>=', $fechaInicio)
            ->whereDate('facturas.fecha_emision', '<=', $fechaFin)
            ->select(
                DB::raw("COALESCE(categorias.categoria, 'General') as categoria"),
                DB::raw('SUM(detalle_factura.subtotal_linea) as total_ventas'),
                DB::raw('SUM(detalle_factura.subtotal_linea - (detalle_factura.cantidad * detalle_factura.precio_unitario_costo)) as ganancia_bruta')
            )
            ->groupBy('categorias.categoria')
            ->orderByDesc('ganancia_bruta')
            ->get();

        // 7. Tendencia temporal de Ventas vs Costos vs Utilidad (para gráfico de líneas)
        $tendenciaVentas = DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->whereDate('facturas.fecha_emision', '>=', $fechaInicio)
            ->whereDate('facturas.fecha_emision', '<=', $fechaFin)
            ->select(
                DB::raw('DATE(facturas.fecha_emision) as fecha'),
                DB::raw('SUM(detalle_factura.subtotal_linea) as ventas'),
                DB::raw('SUM(detalle_factura.cantidad * detalle_factura.precio_unitario_costo) as costos'),
                DB::raw('SUM(detalle_factura.subtotal_linea - (detalle_factura.cantidad * detalle_factura.precio_unitario_costo)) as ganancia')
            )
            ->groupBy(DB::raw('DATE(facturas.fecha_emision)'))
            ->orderBy(DB::raw('DATE(facturas.fecha_emision)'), 'asc')
            ->get();

        // 8. Listado de facturas del período con cálculo de rentabilidad
        $facturas = Factura::with(['cliente', 'detalles.producto'])
            ->whereDate('fecha_emision', '>=', $fechaInicio)
            ->whereDate('fecha_emision', '<=', $fechaFin)
            ->orderByDesc('fecha_emision')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rentabilidad.index', compact(
            'fechaInicio',
            'fechaFin',
            'totalFacturas',
            'totalVentasNetas',
            'totalIva',
            'totalVentasConIva',
            'costoTotal',
            'gananciaBruta',
            'margenPorcentaje',
            'ticketPromedio',
            'topProductos',
            'rentabilidadCategorias',
            'tendenciaVentas',
            'facturas'
        ));
    }
}
