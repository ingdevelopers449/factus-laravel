<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Resolucion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal de control para administradores.
     */
    public function index()
    {
        $hoy = today();
        $mesActual = now()->month;
        $anioActual = now()->year;

        // 1. Métricas de Ventas de Hoy
        $ventasHoy = (float) Factura::whereDate('fecha_emision', $hoy)->sum('total_pagar');
        $facturasHoyCount = Factura::whereDate('fecha_emision', $hoy)->count();

        // 2. Métricas de Ventas del Mes Actual
        $ventasMes = (float) Factura::whereMonth('fecha_emision', $mesActual)
            ->whereYear('fecha_emision', $anioActual)
            ->sum('total_pagar');
        $ventasNetasMes = (float) Factura::whereMonth('fecha_emision', $mesActual)
            ->whereYear('fecha_emision', $anioActual)
            ->sum('subtotal');
        $facturasMesCount = Factura::whereMonth('fecha_emision', $mesActual)
            ->whereYear('fecha_emision', $anioActual)
            ->count();

        // 3. Costo y Utilidad del Mes Actual
        $cogsMes = (float) DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->whereMonth('facturas.fecha_emision', $mesActual)
            ->whereYear('facturas.fecha_emision', $anioActual)
            ->sum(DB::raw('detalle_factura.cantidad * detalle_factura.precio_unitario_costo'));

        $gananciaMes = $ventasNetasMes - $cogsMes;
        $margenMes = $ventasNetasMes > 0 ? ($gananciaMes / $ventasNetasMes) * 100 : 0;

        // 4. Inventario y Alertas de Stock Bajo (<= 5 unidades)
        $totalProductos = Producto::count();
        $productosStockBajo = Producto::with('categoria')
            ->where(function ($q) {
                $q->where('state', 1)->orWhere('state', 'activo');
            })
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
        $conteoStockBajo = Producto::where(function ($q) {
            $q->where('state', 1)->orWhere('state', 'activo');
        })->where('stock', '<=', 5)->count();

        // 5. Clientes
        $totalClientes = Cliente::count();

        // 6. Estado de la Resolución DIAN Activa
        $resolucionActiva = Resolucion::where('estado', 'activa')->first();
        $porcentajeDian = 0;
        $diasVigenciaDian = null;

        if ($resolucionActiva) {
            $rangoTotal = $resolucionActiva->rango_final - $resolucionActiva->rango_inicial + 1;
            $porcentajeDian = $rangoTotal > 0 ? min(round(($resolucionActiva->contador_actual / $rangoTotal) * 100, 1), 100) : 0;
            if ($resolucionActiva->fecha_vigencia) {
                $vigencia = Carbon::parse($resolucionActiva->fecha_vigencia);
                $diasVigenciaDian = Carbon::today()->diffInDays($vigencia, false);
            }
        }

        // 7. Gráfico: Evolución de Ventas últimos 7 días
        $diasUltimos7 = collect(range(6, 0))->map(function ($d) {
            return now()->subDays($d)->format('Y-m-d');
        });

        $ventas7DiasQuery = DB::table('facturas')
            ->whereDate('fecha_emision', '>=', now()->subDays(6)->toDateString())
            ->select(
                DB::raw('DATE(fecha_emision) as fecha'),
                DB::raw('COALESCE(SUM(total_pagar), 0) as total')
            )
            ->groupBy(DB::raw('DATE(fecha_emision)'))
            ->pluck('total', 'fecha');

        $chart7DiasLabels = $diasUltimos7->map(function ($f) {
            return Carbon::parse($f)->format('d M');
        })->values();

        $chart7DiasData = $diasUltimos7->map(function ($f) use ($ventas7DiasQuery) {
            return (float) ($ventas7DiasQuery[$f] ?? 0);
        })->values();

        // 8. Gráfico: Top 5 Categorías más vendidas
        $topCategorias = DB::table('detalle_factura')
            ->join('productos', 'detalle_factura.id_producto', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->select(
                DB::raw("COALESCE(categorias.categoria, 'General') as categoria"),
                DB::raw('SUM(detalle_factura.subtotal_linea) as total')
            )
            ->groupBy('categorias.categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $chartCategoriasLabels = $topCategorias->pluck('categoria');
        $chartCategoriasData = $topCategorias->pluck('total')->map(fn ($val) => (float) $val);

        // 9. Últimas 5 Facturas Registradas
        $ultimasFacturas = Factura::with('cliente', 'detalles')
            ->orderByDesc('fecha_emision')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'ventasHoy',
            'facturasHoyCount',
            'ventasMes',
            'ventasNetasMes',
            'facturasMesCount',
            'gananciaMes',
            'margenMes',
            'totalProductos',
            'productosStockBajo',
            'conteoStockBajo',
            'totalClientes',
            'resolucionActiva',
            'porcentajeDian',
            'diasVigenciaDian',
            'chart7DiasLabels',
            'chart7DiasData',
            'chartCategoriasLabels',
            'chartCategoriasData',
            'ultimasFacturas'
        ));
    }
}
