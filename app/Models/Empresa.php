<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'empresa';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_empresa';

    /**
     * Indica si el modelo debe gestionar timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nit',
        'razon_social',
        'direccion',
        'telefono',
        'email',
        'logo_url',
    ];

    /**
     * Resoluciones DIAN pertenecientes a la empresa.
     */
    public function resoluciones(): HasMany
    {
        return $this->hasMany(Resolucion::class, 'id_empresa', 'id_empresa');
    }
}
