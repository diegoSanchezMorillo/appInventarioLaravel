<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';
    protected $fillable = [
        'producto_id', 'trabajador_id', 'tipo', 'fecha_recogida',
        'fecha_entrega', 'sede_devolucion_id', 'observaciones', 'user_id',
    ];
    protected $casts = [
        'fecha_recogida' => 'date',
        'fecha_entrega'  => 'date',
    ];

    public function producto()       { return $this->belongsTo(Producto::class); }
    public function trabajador()     { return $this->belongsTo(Trabajador::class); }
    public function sedeDevolucion() { return $this->belongsTo(Sede::class, 'sede_devolucion_id'); }
    public function usuario()        { return $this->belongsTo(User::class, 'user_id'); }
}
