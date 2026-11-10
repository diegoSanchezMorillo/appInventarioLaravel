<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Asignacion extends Model
{
    // Una asignación = un producto en manos de un trabajador durante un periodo.
    // Si fecha_entrega está vacía, el trabajador todavía lo tiene.
    // Se indica el nombre de la tabla porque Laravel buscaría "asignacions".
    protected $table = 'asignaciones';
    protected $fillable = [
        'producto_id', 'trabajador_id', 'tipo', 'fecha_recogida',
        'fecha_entrega', 'sede_devolucion_id', 'observaciones', 'user_id',
    ];
    protected $casts = [
        'fecha_recogida' => 'date',
        'fecha_entrega'  => 'date',
    ];

    public function producto()       { return $this->belongsTo(Producto::class)->withTrashed(); } // withTrashed: aunque el producto o el trabajador estén dados de baja, el historial los sigue mostrando
    public function trabajador()     { return $this->belongsTo(Trabajador::class)->withTrashed(); }
    public function sedeDevolucion() { return $this->belongsTo(Sede::class, 'sede_devolucion_id'); }
    public function usuario()        { return $this->belongsTo(User::class, 'user_id'); }

    // Scope: asignaciones abiertas = el trabajador todavía tiene el producto.
    // Uso: Asignacion::abiertas()->get()
    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->whereNull('fecha_entrega');
    }

    // Scope: solo los préstamos temporales (los permanentes no generan alerta de retraso)
    public function scopeTemporales(Builder $query): Builder
    {
        return $query->where('tipo', 'temporal');
    }

    // Días que lleva (o llevó) el producto en manos del trabajador
    public function getDiasAttribute(): int
    {
        return (int) $this->fecha_recogida->diffInDays($this->fecha_entrega ?? now());
    }
}
