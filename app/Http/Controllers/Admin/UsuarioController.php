<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $usuarios = User::with('role')
            ->orderBy('name')
            ->paginate(10);
        
        $roles = Role::orderBy('name')->get();
        return view('admin.usuario.index', compact('usuarios', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDAR LOS DATOS RECIBIDOS
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id'=> ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // CREAR EL USUARIO
        User::create($datos);

        // REGRESAR A LA LISTA CON UN MENSAJE
        return redirect()
            ->route('admin.usuario.index')
            ->with('success', 'Empleado registrado correctamente'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
