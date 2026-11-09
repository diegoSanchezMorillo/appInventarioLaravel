<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignarProductoRequest;
use App\Http\Requests\DevolverProductoRequest;
use App\Models\Asignacion;
use App\Models\Producto;
use App\Models\Sede;
use App\Models\Trabajador;
use App\Services\AsignacionService;
use DomainException;

class AsignacionController extends Controller
{
    public function __construct(private AsignacionService $servicio)
    {
    }

    /* ---------- Asignar ---------- */

    public function create(Producto $producto)
    {
        if ($producto->estado !== 'disponible') {
            return redirect()->route('productos.show', $producto)
                ->with('error', "No se puede asignar: el producto está \"{$producto->estado_texto}\".");
        }

        $trabajadores = Trabajador::where('activo', true)
            ->with('sede')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return view('asignaciones.create', compact('producto', 'trabajadores'));
    }

    public function store(AsignarProductoRequest $request, Producto $producto)
    {
        try {
            $asignacion = $this->servicio->asignar($producto, $request->validated());
        } catch (DomainException $e) {
            return redirect()->route('productos.show', $producto)->with('error', $e->getMessage());
        }

        return redirect()->route('productos.show', $producto)
            ->with('success', "{$producto->referencia} asignado a {$asignacion->trabajador->nombre_completo}.");
    }

    /* ---------- Devolver ---------- */

    public function devolverForm(Asignacion $asignacion)
    {
        if ($asignacion->fecha_entrega !== null) {
            return redirect()->route('productos.show', $asignacion->producto)
                ->with('error', 'Esta devolución ya estaba registrada.');
        }

        $asignacion->load(['producto.sede', 'trabajador']);
        $sedes = Sede::where('activa', true)->orderBy('nombre')->get();

        return view('asignaciones.devolver', compact('asignacion', 'sedes'));
    }

    public function devolver(DevolverProductoRequest $request, Asignacion $asignacion)
    {
        try {
            $this->servicio->devolver($asignacion, $request->validated());
        } catch (DomainException $e) {
            return redirect()->route('productos.show', $asignacion->producto)->with('error', $e->getMessage());
        }

        return redirect()->route('productos.show', $asignacion->producto)
            ->with('success', "Devolución de {$asignacion->producto->referencia} registrada.");
    }
}