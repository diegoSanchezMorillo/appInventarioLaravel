<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
     protected $table = 'categorias';
    protected $fillable = ['nombre', 'prefijo'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    // Incluye los productos dados de baja (soft delete): también tienen referencia
    public function tieneProductos(): bool
    {
        return $this->productos()->withTrashed()->exists();
    }

    // Referencia que recibirá el próximo producto de esta categoría
    public function getSiguienteReferenciaAttribute(): string
    {
        return $this->prefijo . '-' . str_pad($this->contador + 1, 4, '0', STR_PAD_LEFT);
    }
}
