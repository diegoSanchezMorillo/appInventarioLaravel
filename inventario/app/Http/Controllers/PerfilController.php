<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit', ['usuario' => Auth::user()]);
    }

    // Cada usuario solo puede editar SU propio perfil (se usa Auth::user(), nunca un id de la URL)
    public function update(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            // Para cambiar la contraseña hay que escribir la actual (current_password la comprueba)
            'password_actual' => ['nullable', 'required_with:password', 'current_password'],
            'password'        => ['nullable', 'confirmed', Password::min(8)],
        ], [], [
            'name'            => 'nombre',
            'password_actual' => 'contraseña actual',
            'password'        => 'nueva contraseña',
        ]);
        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
        $usuario->name = $request->name;

        if ($request->filled('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        return redirect()->route('perfil.edit')->with('success', 'Perfil actualizado.');
    }
}
