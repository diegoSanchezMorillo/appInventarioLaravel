<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajador extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores';
    protected $fillable = ['nombre', 'apellidos', 'dni', 'email', 'telefono', 'sede_id', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function sede()        { return $this->belongsTo(Sede::class); }
    public function asignaciones() { return $this->hasMany(Asignacion::class); }

    public function asignacionesActivas()
    {
        return $this->hasMany(Asignacion::class)->whereNull('fecha_entrega');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
