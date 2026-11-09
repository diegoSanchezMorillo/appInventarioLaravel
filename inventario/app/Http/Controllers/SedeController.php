<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;
use App\Http\Requests\SedeRequest;

class SedeController extends Controller
{
    public function index()
    {
        $sedes = Sede::withCount(['productos', 'trabajadores'])
            ->orderBy('nombre')
            ->paginate(15);

        return view('sedes.index', compact('sedes'));
    }

    public function create()
    {
        return view('sedes.create', ['sede' => new Sede(['activa' => true])]);
    }

    public function store(SedeRequest $request)
    {
        Sede::create($request->validated() + ['activa' => $request->boolean('activa')]);

        return redirect()->route('sedes.index')->with('success', 'Sede creada correctamente.');
    }

    public function edit(Sede $sede)
    {
        return view('sedes.edit', compact('sede'));
    }

    public function update(SedeRequest $request, Sede $sede)
    {
        $sede->update($request->validated() + ['activa' => $request->boolean('activa')]);

        return redirect()->route('sedes.index')->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Sede $sede)
    {
        if ($sede->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar una sede con productos. Muévelos antes o desactívala.');
        }

        $sede->delete();

        return redirect()->route('sedes.index')->with('success', 'Sede eliminada.');
    }
}
