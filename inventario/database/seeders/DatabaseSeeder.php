<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario administrador inicial. Es solo para desarrollo: en producción
        // cambia esta contraseña (o crea el admin a mano) antes de usar la aplicación.
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@inventario.test',
            'password' => Hash::make('Sastipem12'),
            'es_admin' => true,
        ]);

        // El orden importa: primero lo que otros necesitan
        $this->call([
            SedeSeeder::class,
            CategoriaSeeder::class,
            TrabajadorSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}
