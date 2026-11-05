<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleBorrador extends Model
{
    protected $table = 'detalle_borrador';
    protected $primaryKey = 'id_detalle_borrador';
    public $timestamps = false;

    protected $fillable = [
        'id_borrador',
        'id_producto',
        'cantidad',
    ];

    // Relación con VentaBorrador
    public function borrador(): BelongsTo
    {
        return $this->belongsTo(
            VentaBorrador::class,
            'id_borrador',
            'id_borrador'
        );
    }

    // Relación con Producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }
}