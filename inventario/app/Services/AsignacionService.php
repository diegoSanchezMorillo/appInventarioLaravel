<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Producto;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AsignacionService
{
    /**
     * Entrega un producto a un trabajador.
     *
     * @throws DomainException si el producto no se puede asignar
     */
    public function asignar(Producto $producto, array $datos): Asignacion
    {
        return DB::transaction(function () use ($producto, $datos) {
            // Recargamos el producto bloqueándolo: nadie más puede tocarlo hasta que terminemos
            $producto = Producto::lockForUpdate()->findOrFail($producto->id);

            if ($producto->estado !== 'disponible') {
                throw new DomainException("El producto {$producto->referencia} no está disponible ({$producto->estado_texto}).");
            }

            if ($producto->asignacionActiva()->exists()) {
                throw new DomainException("El producto {$producto->referencia} ya lo tiene otro trabajador.");
            }

            $asignacion = $producto->asignaciones()->create([
                'trabajador_id'  => $datos['trabajador_id'],
                'tipo'           => $datos['tipo'],
                'fecha_recogida' => $datos['fecha_recogida'],
                'observaciones'  => $datos['observaciones'] ?? null,
                'user_id' => Auth::id(),
            ]);

            $producto->update(['estado' => 'asignado']);

            return $asignacion;
        });
    }

    /**
     * Registra la devolución de un producto.
     *
     * @throws DomainException si la asignación ya estaba cerrada
     */
    public function devolver(Asignacion $asignacion, array $datos): void
    {
        DB::transaction(function () use ($asignacion, $datos) {
            $asignacion = Asignacion::lockForUpdate()->findOrFail($asignacion->id);

            if ($asignacion->fecha_entrega !== null) {
                throw new DomainException('Esta devolución ya estaba registrada.');
            }

            // Las notas de la devolución se añaden a las de la asignación, sin borrarlas
            $observaciones = $asignacion->observaciones;
            if (! empty($datos['observaciones'])) {
                $observaciones = trim($observaciones . "\nDevolución: " . $datos['observaciones']);
            }

            $asignacion->update([
                'fecha_entrega'      => $datos['fecha_entrega'],
                'sede_devolucion_id' => $datos['sede_devolucion_id'],
                'observaciones'      => $observaciones,
            ]);

            $asignacion->producto->update([
                'estado'  => $datos['estado'],
                'sede_id' => $datos['sede_devolucion_id'],
            ]);
        });
    }
}