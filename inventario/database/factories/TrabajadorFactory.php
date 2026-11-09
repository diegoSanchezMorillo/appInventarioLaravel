<?php

namespace Database\Factories;

use App\Models\Trabajador;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sede;

/**
 * @extends Factory<Trabajador>
 */
class TrabajadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'nombre'    => fake()->firstName(),
            'apellidos' => fake()->lastName() . ' ' . fake()->lastName(),
            'dni'       => fake()->unique()->dni(),
            'email'     => fake()->unique()->safeEmail(),
            'telefono'  => fake()->mobileNumber(),
            'sede_id'   => Sede::inRandomOrder()->value('id'),
            'activo'    => true,
        ];
    }
}
