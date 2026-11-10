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
    // La lógica de negocio (cambiar estados, guardar fechas...) vive en AsignacionService.
    // El controlador solo recibe la petición, llama al servicio y decide a dónde redirigir.
    // Inyección de dependencias: Laravel crea el AsignacionService y lo pasa aquí solo.
    public function __construct(private AsignacionService $servicio)
    {
    }

    /* ---------- Asignar ---------- */

    // Muestra el formulario para asignar un producto a un trabajador
    public function create(Producto $producto)
    {
        // Solo se pueden asignar productos "disponibles" (no los ya asignados, en reparación o de baja).
        // Esto cubre también a quien escribe la URL a mano en el navegador.
        // Es una doble comprobación: aquí solo se avisa antes de mostrar el formulario;
        // la que garantiza que no haya dobles asignaciones es la del servicio, dentro del bloqueo.
        if ($producto->estado !== 'disponible') {
            return redirect()->route('productos.show', $producto)
                ->with('error', "No se puede asignar: el producto está \"{$producto->estado_texto}\".");
        }

        // Solo trabajadores activos; los dados de baja (soft delete) no salen en el desplegable
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
            // $request->validated() solo contiene los campos que han pasado la validación
            // (AsignarProductoRequest). Si el servicio detecta un problema lanza DomainException.
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
        // Si ya tiene fecha de entrega, la devolución se hizo antes (p. ej. pulsando "atrás"
        // o escribiendo la URL a mano): no se permite registrarla dos veces
        if ($asignacion->fecha_entrega !== null) {
            return redirect()->route('productos.show', $asignacion->producto)
                ->with('error', 'Esta devolución ya estaba registrada.');
        }

        $asignacion->load(['producto.sede', 'trabajador']);
        // Solo sedes activas: el producto puede devolverse en una sede distinta a la de origen
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