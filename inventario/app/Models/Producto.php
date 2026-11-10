<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADOS = [
        'disponible' => 'Disponible',
        'asignado'   => 'Asignado',
        'reparacion' => 'En reparación',
        'baja'       => 'De baja',
    ];

    public const COLORES_ESTADO = [
        'disponible' => 'success',
        'asignado'   => 'primary',
        'reparacion' => 'warning',
        'baja'       => 'secondary',
    ];

    protected $table = 'productos';

    protected $fillable = [
        'nombre', 'marca', 'modelo', 'numero_serie', 'descripcion',
        'foto', 'categoria_id', 'sede_id', 'estado', 'fecha_alta',
    ];

    protected $casts = [
        'fecha_alta' => 'date',
    ];

    protected static function booted(): void
    {
        // Al crear un producto se genera su referencia (ej. ORD-0001):
        // prefijo de la categoría + siguiente número. La categoría se bloquea
        // (lockForUpdate) para que dos altas simultáneas no obtengan el mismo número.
        static::creating(function (Producto $producto) {
            DB::transaction(function () use ($producto) {
                $categoria = Categoria::lockForUpdate()->findOrFail($producto->categoria_id);
                $categoria->increment('contador');

                $producto->referencia = $categoria->prefijo . '-' . str_pad($categoria->contador, 4, '0', STR_PAD_LEFT);
            });

            $producto->fecha_alta ??= now();
        });
    }

    /* ---------- Relaciones ---------- */

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    // La asignación abierta del producto (como mucho una): dice quién lo tiene ahora.
    // Si es null, el producto está físicamente en su sede.
    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class)->whereNull('fecha_entrega');
    }

    /* ---------- Ubicación (no se guarda, se calcula) ---------- */

    public function getEstaConTrabajadorAttribute(): bool
    {
        return $this->asignacionActiva !== null;
    }

    public function getUbicacionAttribute(): string
    {
        if ($this->esta_con_trabajador) {
            return $this->asignacionActiva->trabajador->nombre_completo;
        }

        return $this->sede?->nombre ?? 'Sin ubicación';
    }

    /* ---------- Estado legible ---------- */

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getEstadoColorAttribute(): string
    {
        return self::COLORES_ESTADO[$this->estado] ?? 'light';
    }

    /* ---------- Scopes para buscar y filtrar ---------- */

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (blank($texto)) {
            return $query;
        }

        return $query->where(function ($q) use ($texto) {
            $q->where('referencia', 'like', "%{$texto}%")
              ->orWhere('nombre', 'like', "%{$texto}%")
              ->orWhere('numero_serie', 'like', "%{$texto}%")
              ->orWhere('marca', 'like', "%{$texto}%")
              ->orWhere('modelo', 'like', "%{$texto}%");
        });
    }

    // Lo que está físicamente en la sede ahora mismo (no lo tiene nadie).
    // Hacen falta las dos condiciones: un producto asignado conserva su sede_id
    // como sede de referencia, pero en realidad está con el trabajador.
    public function scopeEnSede(Builder $query, int $sedeId): Builder
    {
        return $query->where('sede_id', $sedeId)->whereDoesntHave('asignacionActiva');
    }

    // Lo que tiene un trabajador ahora mismo
    public function scopeConTrabajador(Builder $query, int $trabajadorId): Builder
    {
        return $query->whereHas('asignacionActiva', fn ($q) => $q->where('trabajador_id', $trabajadorId));
    }
}