<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    /**
     * Muestra la vista principal con los datos de la empresa.
     */
    public function index(): View
    {
        $empresa = Empresa::first();

        return view('admin.empresas.index', compact('empresa'));
    }

    /**
     * Guarda o actualiza los datos de la empresa.
     */
    public function update(Request $request): RedirectResponse
    {
        $empresa = Empresa::first();

        $rules = [
            'nit' => [
                'required',
                'string',
                'max:30',
                $empresa
                    ? Rule::unique('empresa', 'nit')->ignore($empresa->id_empresa, 'id_empresa')
                    : 'unique:empresa,nit',
            ],
            'razon_social' => ['required', 'string', 'max:200'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ];

        $datos = $request->validate($rules);

        // Si se subió un archivo de logo nuevo
        if ($request->hasFile('logo')) {
            $archivo = $request->file('logo');
            $nombreArchivo = 'logo_empresa_' . time() . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs('empresa', $nombreArchivo, 'public');
            $datos['logo_url'] = '/storage/' . $ruta;
        }

        unset($datos['logo']);

        if ($empresa) {
            $empresa->update($datos);
        } else {
            Empresa::create($datos);
        }

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Datos de la empresa guardados correctamente.');
    }
}
