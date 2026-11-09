<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Sede extends Model
{
    protected $table = 'sedes';
    protected $fillable = ['nombre', 'direccion', 'localidad', 'telefono', 'activa'];
    protected $casts = ['activa' => 'boolean'];

    public function productos()   { return $this->hasMany(Producto::class); }
    public function trabajadores() { return $this->hasMany(Trabajador::class); }
}
