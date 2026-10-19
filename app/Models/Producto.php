<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'codigo_barras',
        'producto',
        'precio_compra',
        'precio_venta',
        'stock',
        'state',
        'iva',
        'imagen_url',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock' => 'integer',
            'state' => 'boolean',
            'iva' => 'decimal:2',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            Categoria::class,
            'id_categoria',
            'id_categoria'
        );
    }
}
