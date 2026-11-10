<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Campos que se pueden rellenar en bloque (User::create([...])).
    // es_admin: puede gestionar usuarios. activo: si es false, no puede iniciar sesión.
    protected $fillable = [
    'name',
    'email',
    'password',
    'es_admin',
    'activo',
    ];

// Conversión automática de tipos al leer/guardar en la base de datos
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        // "hashed": la contraseña se cifra sola al asignarla, nunca se guarda en texto plano
        'password'          => 'hashed',
        'es_admin'          => 'boolean',
        'activo'            => 'boolean',
    ];
}
}
