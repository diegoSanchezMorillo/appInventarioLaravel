<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
     use HasFactory, SoftDeletes;

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
        static::creating(function (Producto $producto) {
            DB::transaction(function () use ($producto) {
                // Bloquea la categoría para que dos altas a la vez no cojan el mismo número
                $categoria = Categoria::lockForUpdate()->findOrFail($producto->categoria_id);
                $categoria->increment('contador');

                $producto->referencia = $categoria->prefijo . '-' . str_pad($categoria->contador, 4, '0', STR_PAD_LEFT);
            });

            $producto->fecha_alta ??= now();
        });
    }

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

    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class)->whereNull('fecha_entrega');
    }
}
