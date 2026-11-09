<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categoria;
use App\Models\Sede;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'       => ucfirst(fake()->words(3, true)),
            'marca'        => fake()->randomElement(['HP', 'Dell', 'Lenovo', 'Asus', 'Epson', 'BenQ', 'LG', 'Logitech']),
            'modelo'       => strtoupper(fake()->bothify('??-###')),
            'numero_serie' => fake()->unique()->bothify('SN-####-????'),
            'descripcion'  => fake()->optional()->sentence(),
            'categoria_id' => Categoria::inRandomOrder()->value('id'),
            'sede_id'      => Sede::inRandomOrder()->value('id'),
            'estado'       => 'disponible',
        ];
    }
}
