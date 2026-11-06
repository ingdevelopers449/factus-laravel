<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VentaBorrador extends Model
{
    protected $table = 'ventas_borrador';

    protected $primaryKey = 'id_borrador';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_cliente',
        'fecha_actualizacion',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(
            DetalleBorrador::class,
            'id_borrador',
            'id_borrador'
        );
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_usuario',
            'id'
        );
    }
}
