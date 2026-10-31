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
        'id_prudcto',
        'cantidad',
        'precio_unitario_venta',
        'precio_unitatio_costo',
        'subtotal_linea'
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'id_factura', 'id');
    }


}
