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
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@inventario.test',
            'password' => Hash::make('password')
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
