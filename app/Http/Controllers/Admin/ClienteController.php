<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');

        $clientes = Cliente::query()
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('identificacion', 'like', "%{$buscar}%")
                        ->orWhere('nombre_razon_social', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre_razon_social')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'buscar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'identificacion' => [
                'required',
                'string',
                'max:20',
                'unique:clientes,identificacion',
            ],
            'nombre_razon_social' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        Cliente::create($datos);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // Buscar el cliente por su clave primaria
        $cliente = Cliente::findOrFail($id);

        // Validar los datos recibidos
        $datos = $request->validate([
            'identificacion' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes', 'identificacion')
                    ->ignore($cliente->id_cliente, 'id_cliente'),
            ],
            'nombre_razon_social' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        // Actualizar el registro en la base de datos
        $cliente->update($datos);

        // Regresar al listado con mensaje de éxito
        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
