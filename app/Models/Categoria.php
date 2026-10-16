<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'categoria',
        'descripcion',
        'state',
    ];

    protected function casts(): Array
    {
        return [
            'state' => 'boolean',
        ];
    }
}
