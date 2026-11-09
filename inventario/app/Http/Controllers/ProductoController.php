<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Sede;
use App\Models\Trabajador;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Atajo: si escriben una referencia exacta, directos a la ficha
        if ($request->filled('buscar')) {
            $exacto = Producto::where('referencia', strtoupper(trim($request->buscar)))->first();
            if ($exacto) {
                return redirect()->route('productos.show', $exacto);
            }
        }

        $productos = Producto::with(['categoria', 'sede', 'asignacionActiva.trabajador'])
            ->buscar($request->buscar)
            ->when($request->categoria_id, fn ($q, $id) => $q->where('categoria_id', $id))
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->ubicacion, function ($q, $ubicacion) {
                // Valores del filtro: "s-3" = sede 3, "t-5" = trabajador 5
                [$tipo, $id] = array_pad(explode('-', $ubicacion, 2), 2, null);

                if ($tipo === 's') {
                    $q->enSede((int) $id);
                } elseif ($tipo === 't') {
                    $q->conTrabajador((int) $id);
                }
            })
            ->orderBy('referencia')
            ->paginate(20)
            ->withQueryString();

        return view('productos.index', [
            'productos'    => $productos,
            'categorias'   => Categoria::orderBy('nombre')->get(),
            'sedes'        => Sede::orderBy('nombre')->get(),
            'trabajadores' => Trabajador::whereHas('asignacionesActivas')->orderBy('apellidos')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create', [
            'producto'   => new Producto(['estado' => 'disponible', 'fecha_alta' => now()]),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'sedes'      => $this->sedesParaFormulario(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        return redirect()->route('productos.show', $producto)
            ->with('success', "Producto creado con la referencia {$producto->referencia}.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'sede', 'asignacionActiva.trabajador.sede']);

        $historial = $producto->asignaciones()
            ->with(['trabajador', 'sedeDevolucion'])
            ->latest('fecha_recogida')
            ->latest('id')
            ->get();

        return view('productos.show', compact('producto', 'historial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $producto->load('asignacionActiva.trabajador');

        return view('productos.edit', [
            'producto' => $producto,
            'sedes'    => $this->sedesParaFormulario($producto->sede_id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        if ($producto->asignaciones()->exists()) {
            return back()->with('error', 'Este producto tiene historial de asignaciones. En lugar de eliminarlo, cambia su estado a "De baja".');
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', "Producto {$producto->referencia} eliminado.");
    
    }

    private function sedesParaFormulario(?int $sedeActual = null)
    {
        return Sede::where('activa', true)
            ->when($sedeActual, fn ($q) => $q->orWhere('id', $sedeActual))
            ->orderBy('nombre')
            ->get();
    }
}
