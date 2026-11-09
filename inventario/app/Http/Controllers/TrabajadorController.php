<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrabajadorRequest;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use App\Models\Sede;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trabajadores = Trabajador::with('sede')
            ->withCount('asignacionesActivas')
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('apellidos', 'like', "%{$buscar}%")
                      ->orWhere('dni', 'like', "%{$buscar}%");
                });
            })
            ->when($request->sede_id, fn ($query, $sedeId) => $query->where('sede_id', $sedeId))
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $sedes = Sede::orderBy('nombre')->get();

        return view('trabajadores.index', compact('trabajadores', 'sedes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trabajadores.create', [
            'trabajador' => new Trabajador(['activo' => true]),
            'sedes'      => $this->sedesParaFormulario(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrabajadorRequest $request)
    {
        $trabajador = Trabajador::create($request->validated() + ['activo' => $request->boolean('activo')]);

        return redirect()->route('trabajadores.show', $trabajador)->with('success', 'Trabajador creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trabajador $trabajador)
    {
        $trabajador->load([
            'sede',
            'asignacionesActivas.producto.categoria',
        ]);

        $historial = $trabajador->asignaciones()
            ->with('producto')
            ->whereNotNull('fecha_entrega')
            ->latest('fecha_recogida')
            ->get();

        return view('trabajadores.show', compact('trabajador', 'historial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trabajador $trabajador)
    {
        return view('trabajadores.edit', [
            'trabajador' => $trabajador,
            'sedes'      => $this->sedesParaFormulario($trabajador->sede_id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrabajadorRequest $request, Trabajador $trabajador)
    {
        $trabajador->update($request->validated() + ['activo' => $request->boolean('activo')]);

        return redirect()->route('trabajadores.show', $trabajador)->with('success', 'Trabajador actualizado correctamente.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trabajador $trabajador)
    {
        $pendientes = $trabajador->asignacionesActivas()->count();

        if ($pendientes > 0) {
            return back()->with('error', "No se puede dar de baja: tiene {$pendientes} producto(s) sin devolver.");
        }

        $trabajador->delete(); // soft delete: conserva su historial

        return redirect()->route('trabajadores.index')->with('success', 'Trabajador dado de baja.');
    
    }

    // Sedes activas + la sede actual del trabajador aunque esté inactiva
    private function sedesParaFormulario(?int $sedeActual = null)
    {
        return Sede::where('activa', true)
            ->when($sedeActual, fn ($q) => $q->orWhere('id', $sedeActual))
            ->orderBy('nombre')
            ->get();
    }
}
