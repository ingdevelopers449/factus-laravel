<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $primaryKey = 'id';

    public $timestamps = false;


    protected $fillable = [
        'id_empresa',
        'id_resolucion',
        'prefijo_resolucion',
        'consecutivo',
        'fecha_emision',
        'id_cliente',
        'id_usuario',
        'subtotal',
        'total_iva',
        'total_pagar',
        'porcentaje_comision_aplicado',
        'cufe',
        'codigo_qr',
        'estado_dian',
        'motivo_rechazo',
        'fecha_validacion_dian',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'id_factura', 'id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
