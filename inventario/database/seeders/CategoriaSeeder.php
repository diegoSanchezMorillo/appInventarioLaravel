<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categorias = [
            ['nombre' => 'Ordenadores',      'prefijo' => 'ORD'],
            ['nombre' => 'Portátiles',       'prefijo' => 'POR'],
            ['nombre' => 'Videoproyectores', 'prefijo' => 'PRY'],
            ['nombre' => 'Monitores',        'prefijo' => 'MON'],
            ['nombre' => 'Ratones',          'prefijo' => 'RAT'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
