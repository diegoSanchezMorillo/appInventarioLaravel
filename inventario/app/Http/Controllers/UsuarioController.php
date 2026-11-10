<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Primero los usuarios activos y, dentro de cada grupo, por orden alfabético
        $usuarios = User::orderByDesc('activo')->orderBy('name')->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usuarios.create', ['usuario' => new User(['activo' => true])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuarioRequest $request)
    {
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'es_admin' => $request->boolean('es_admin'),
            'activo'   => $request->boolean('activo'),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuarioRequest $request, User $usuario)
    {
        $datos = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $datos['password'] = $request->password;
        }

        // Nadie puede quitarse a sí mismo el rol de admin ni desactivarse
        if (! $usuario->is(Auth::user())) {
            $datos['es_admin'] = $request->boolean('es_admin');
            $datos['activo']   = $request->boolean('activo');
        }

        // Solo se incluyen en $datos los campos que se van a cambiar:
        // la contraseña se cambia únicamente si se ha escrito una nueva
        $usuario->update($datos);

        // Si se desactiva, se cierran sus sesiones abiertas al momento
        if (! $usuario->activo) {
            DB::table('sessions')->where('user_id', $usuario->id)->delete();
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Sin implementar a propósito: la ruta está excluida en web.php.
        // Los usuarios no se borran, se desactivan desde "Editar" (campo "activo").
    }
}
