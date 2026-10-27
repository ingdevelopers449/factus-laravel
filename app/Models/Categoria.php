<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    const CREATED_AT = 'fecha_creacion';

    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'categorias';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'categoria',
        'descripcion',
        'state',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'boolean',
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }

    public function getCreatedAtAttribute(): mixed
    {
        return $this->fecha_creacion;
    }

    public function getUpdatedAtAttribute(): mixed
    {
        return $this->fecha_actualizacion;
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}
