<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Cliente;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(): View{
        $clientes = Cliente::orderBy('nombre_razon_social')
            ->paginate(10);

        return view('admin.clientes.index', compact('clientes'));
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
        ->route('admin.clientes.index')
        ->with('success', 'Cliente registrado correctamente.'); 
    }
}
