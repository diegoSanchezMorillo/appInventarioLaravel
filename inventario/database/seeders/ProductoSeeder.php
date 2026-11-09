<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asignacion;
use App\Models\Producto;
use App\Models\Trabajador;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::factory()->count(60)->create();

        // Asigna 10 productos al azar a trabajadores al azar
        Producto::inRandomOrder()->take(10)->get()->each(function (Producto $producto) {
            Asignacion::create([
                'producto_id'    => $producto->id,
                'trabajador_id'  => Trabajador::inRandomOrder()->value('id'),
                'tipo'           => fake()->randomElement(['temporal', 'permanente']),
                'fecha_recogida' => fake()->dateTimeBetween('-6 months', 'now'),
            ]);

            $producto->update(['estado' => 'asignado']);
        });
    }
}
