<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'state' => ['nullable', 'boolean'],
        ]);

        // Si se envió state en el formulario se toma, o por defecto true (activo)
        $datos['state'] = $request->has('state') ? $request->boolean('state') : true;

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
    public function update(Request $request, string $id): RedirectResponse
    {
        $usuario = User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'state' => ['nullable', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $datos = $request->validate($rules);

        if ($request->has('state')) {
            $datos['state'] = $request->boolean('state');
        }

        $usuario->update($datos);

        return redirect()
            ->route('admin.usuario.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()
            ->route('admin.usuario.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}
