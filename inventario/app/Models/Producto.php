<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';
    protected $fillable = [
        'nombre', 'marca', 'modelo', 'numero_serie', 'descripcion',
        'foto', 'categoria_id', 'sede_id', 'estado', 'fecha_alta',
    ];
    protected $casts = ['fecha_alta' => 'date'];

    public function categoria()    { return $this->belongsTo(Categoria::class); }
    public function sede()         { return $this->belongsTo(Sede::class); }
    public function asignaciones() { return $this->hasMany(Asignacion::class); }

    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class)->whereNull('fecha_entrega');
    }
}
