<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Resolucion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParametrizacionController extends Controller
{
    /**
     * Muestra la vista principal de parametrización DIAN con resoluciones y KPIs.
     */
    public function index(Request $request): View
    {
        $empresa = Empresa::first() ?? new Empresa;
        $buscar = $request->query('buscar');

        $query = Resolucion::query();

        if ($empresa->id_empresa) {
            $query->where('id_empresa', $empresa->id_empresa);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_resolucion', 'like', "%{$buscar}%")
                    ->orWhere('prefijo', 'like', "%{$buscar}%");
            });
        }

        $resoluciones = $query->orderByDesc('id_resolucion')
            ->paginate(10)
            ->withQueryString();

        // Resolución activa principal para métricas rápidas (KPIs)
        $resolucionActiva = Resolucion::when($empresa->id_empresa, function ($q) use ($empresa) {
            $q->where('id_empresa', $empresa->id_empresa);
        })
            ->where('estado', 'activa')
            ->orderByDesc('id_resolucion')
            ->first();

        return view('admin.parametrizacion.index', compact('empresa', 'resoluciones', 'resolucionActiva', 'buscar'));
    }

    /**
     * Almacena una nueva resolución DIAN en el sistema.
     */
    public function store(Request $request): RedirectResponse
    {
        $empresa = Empresa::first();

        if (! $empresa) {
            return redirect()
                ->route('parametrizacion.index')
                ->with('error', 'Debes configurar los datos de la empresa antes de registrar resoluciones DIAN.');
        }

        $request->validate([
            'numero_resolucion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('resolucion_dian', 'numero_resolucion')
                    ->where('id_empresa', $empresa->id_empresa),
            ],
            'prefijo' => 'required|string|max:20',
            'fecha_vigencia' => 'required|date',
            'rango_inicial' => 'required|integer|min:1',
            'rango_final' => 'required|integer|gte:rango_inicial',
            'contador_actual' => 'required|integer|min:0',
            'estado' => 'required|in:activa,inactiva',
        ], [
            'numero_resolucion.unique' => 'Ya existe una resolución con este número para la empresa.',
            'rango_final.gte' => 'El rango final debe ser mayor o igual al rango inicial.',
        ]);

        $prefijo = strtoupper(trim($request->string('prefijo')->value()));
        $estado = $request->string('estado')->value();

        // Si se registra como activa, inactivamos cualquier otra resolución previa con el mismo prefijo
        if ($estado === 'activa') {
            Resolucion::where('id_empresa', $empresa->id_empresa)
                ->where('prefijo', $prefijo)
                ->where('estado', 'activa')
                ->update(['estado' => 'inactiva']);
        }

        Resolucion::create([
            'id_empresa' => $empresa->id_empresa,
            'numero_resolucion' => trim($request->string('numero_resolucion')->value()),
            'prefijo' => $prefijo,
            'fecha_vigencia' => $request->fecha_vigencia,
            'rango_inicial' => (int) $request->rango_inicial,
            'rango_final' => (int) $request->rango_final,
            'contador_actual' => (int) $request->contador_actual,
            'estado' => $estado,
        ]);

        return redirect()
            ->route('parametrizacion.index')
            ->with('success', 'Resolución DIAN registrada correctamente.');
    }

    /**
     * Actualiza una resolución DIAN existente.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $resolucion = Resolucion::findOrFail($id);

        $request->validate([
            'numero_resolucion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('resolucion_dian', 'numero_resolucion')
                    ->where('id_empresa', $resolucion->id_empresa)
                    ->ignore($resolucion->id_resolucion, 'id_resolucion'),
            ],
            'prefijo' => 'required|string|max:20',
            'fecha_vigencia' => 'required|date',
            'rango_inicial' => 'required|integer|min:1',
            'rango_final' => 'required|integer|gte:rango_inicial',
            'contador_actual' => 'required|integer|min:0',
            'estado' => 'required|in:activa,inactiva',
        ], [
            'numero_resolucion.unique' => 'Ya existe una resolución con este número para la empresa.',
            'rango_final.gte' => 'El rango final debe ser mayor o igual al rango inicial.',
        ]);

        $prefijo = strtoupper(trim($request->string('prefijo')->value()));
        $estado = $request->string('estado')->value();

        // Si se cambia a activa, inactivamos las demás con el mismo prefijo
        if ($estado === 'activa' && $resolucion->estado !== 'activa') {
            Resolucion::where('id_empresa', $resolucion->id_empresa)
                ->where('prefijo', $prefijo)
                ->where('id_resolucion', '!=', $resolucion->id_resolucion)
                ->where('estado', 'activa')
                ->update(['estado' => 'inactiva']);
        }

        $resolucion->update([
            'numero_resolucion' => trim($request->string('numero_resolucion')->value()),
            'prefijo' => $prefijo,
            'fecha_vigencia' => $request->fecha_vigencia,
            'rango_inicial' => (int) $request->rango_inicial,
            'rango_final' => (int) $request->rango_final,
            'contador_actual' => (int) $request->contador_actual,
            'estado' => $estado,
        ]);

        return redirect()
            ->route('parametrizacion.index')
            ->with('success', 'Resolución DIAN actualizada correctamente.');
    }

    /**
     * Elimina una resolución si no tiene facturas asociadas.
     */
    public function destroy(string $id): RedirectResponse
    {
        $resolucion = Resolucion::findOrFail($id);

        $tieneFacturas = DB::table('facturas')
            ->where('id_resolucion', $resolucion->id_resolucion)
            ->exists();

        if ($tieneFacturas) {
            return redirect()
                ->route('parametrizacion.index')
                ->with('error', 'No es posible eliminar la resolución DIAN porque ya cuenta con facturas emitidas. Puedes cambiar su estado a Inactiva.');
        }

        $resolucion->delete();

        return redirect()
            ->route('parametrizacion.index')
            ->with('success', 'Resolución DIAN eliminada correctamente.');
    }

    /**
     * Alterna rápidamente el estado entre activa e inactiva.
     */
    public function toggleEstado(string $id): RedirectResponse
    {
        $resolucion = Resolucion::findOrFail($id);
        $nuevoEstado = $resolucion->estado === 'activa' ? 'inactiva' : 'activa';

        if ($nuevoEstado === 'activa') {
            Resolucion::where('id_empresa', $resolucion->id_empresa)
                ->where('prefijo', $resolucion->prefijo)
                ->where('id_resolucion', '!=', $resolucion->id_resolucion)
                ->where('estado', 'activa')
                ->update(['estado' => 'inactiva']);
        }

        $resolucion->update(['estado' => $nuevoEstado]);

        return redirect()
            ->route('parametrizacion.index')
            ->with('success', "La resolución ahora se encuentra {$nuevoEstado}.");
    }
}
