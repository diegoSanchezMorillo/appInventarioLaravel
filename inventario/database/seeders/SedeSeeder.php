<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sede;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sede::create(['nombre' => 'Sede 1', 'localidad' => 'Martín de la Jara']);
        Sede::create(['nombre' => 'Sede 2', 'localidad' => 'Gilena']);
        Sede::create(['nombre' => 'Sede 3', 'localidad' => 'El saucejo']);
        Sede::create(['nombre' => 'Almacén', 'localidad' => 'Almacén']);
    }
}
