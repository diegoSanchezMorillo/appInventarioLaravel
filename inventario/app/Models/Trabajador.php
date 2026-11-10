<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trabajador extends Model
{
    use HasFactory, SoftDeletes;

    // SoftDeletes: "dar de baja" no borra la fila, solo rellena deleted_at.
    // Así el historial de asignaciones sigue mostrando quién tuvo cada producto.
    protected $table = 'trabajadores';
    protected $fillable = ['nombre', 'apellidos', 'dni', 'email', 'telefono', 'sede_id', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function sede()        { return $this->belongsTo(Sede::class); }
    public function asignaciones() { return $this->hasMany(Asignacion::class); }

    // Productos que el trabajador tiene ahora mismo (sin fecha de entrega).
    // Se usa para impedir darlo de baja mientras tenga algo sin devolver.
    public function asignacionesActivas()
    {
        return $this->hasMany(Asignacion::class)->whereNull('fecha_entrega');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
