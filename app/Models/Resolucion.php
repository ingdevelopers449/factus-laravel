<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resolucion extends Model
{
    /**
     * Nombre de la tabla asociada.
     *
     * @var string
     */
    protected $table = 'resolucion_dian';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_resolucion';

    /**
     * Desactivar gestión automática de timestamps de Eloquent.
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
        'id_empresa',
        'numero_resolucion',
        'fecha_vigencia',
        'prefijo',
        'rango_inicial',
        'rango_final',
        'contador_actual',
        'estado',
    ];

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_vigencia' => 'date',
            'rango_inicial' => 'integer',
            'rango_final' => 'integer',
            'contador_actual' => 'integer',
        ];
    }

    /**
     * Empresa a la que pertenece la resolución.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Cantidad total de folios / números autorizados en el rango.
     */
    public function totalFolios(): int
    {
        return max(0, ($this->rango_final - $this->rango_inicial) + 1);
    }

    /**
     * Cantidad de facturas emitidas registradas por el contador.
     */
    public function facturasEmitidas(): int
    {
        if ($this->contador_actual < $this->rango_inicial) {
            return 0;
        }

        return max(0, ($this->contador_actual - $this->rango_inicial) + 1);
    }

    /**
     * Cantidad de números restantes disponibles para facturar.
     */
    public function facturasDisponibles(): int
    {
        $ultimoConsecutivo = max((int) $this->contador_actual, (int) $this->rango_inicial - 1);

        return max(0, (int) $this->rango_final - $ultimoConsecutivo);
    }

    /**
     * Porcentaje de uso del rango autorizado (0.0% a 100.0%).
     */
    public function porcentajeConsumido(): float
    {
        $total = $this->totalFolios();
        if ($total <= 0) {
            return 0.0;
        }

        $porcentaje = ($this->facturasEmitidas() / $total) * 100;

        return min(100.0, max(0.0, round($porcentaje, 1)));
    }

    /**
     * Días restantes antes del vencimiento de la resolución.
     */
    public function diasRestantes(): int
    {
        if (! $this->fecha_vigencia) {
            return 0;
        }

        $dias = (int) now()->startOfDay()->diffInDays($this->fecha_vigencia->copy()->startOfDay(), false);

        return max(0, $dias);
    }

    /**
     * Determina si la resolución está vigente en fecha y activa.
     */
    public function esVigente(): bool
    {
        if ($this->estado !== 'activa') {
            return false;
        }

        if (! $this->fecha_vigencia) {
            return false;
        }

        return $this->fecha_vigencia->startOfDay()->greaterThanOrEqualTo(now()->startOfDay());
    }
}
