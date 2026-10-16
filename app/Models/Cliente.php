<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'id_cliente';

    public $timestamps = false;
    
    protected $fillable =[
        'identification',
        'nombre_razon_social',
        'email',
        'direccion',
        'telefono',
    ];
}
