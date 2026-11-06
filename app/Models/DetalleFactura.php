<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFactura extends Model
{
    protected $table = 'detalle_factura';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'id_producto',
        'cantidad',
        'precio_unitario_venta',
        'precio_unitario_costo',
        'subtotal_linea',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'id_factura', 'id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
}
