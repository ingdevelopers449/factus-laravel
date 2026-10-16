<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
