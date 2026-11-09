<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Sede;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $diasAlerta = config('inventario.dias_alerta_prestamo');

        // Totales por estado: ['disponible' => 40, 'asignado' => 10, ...]
        $porEstado = Producto::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // Préstamos temporales abiertos desde hace más de X días, los más antiguos primero
        $prestamosLargos = Asignacion::abiertas()
            ->temporales()
            ->whereDate('fecha_recogida', '<=', now()->subDays($diasAlerta))
            ->with(['producto', 'trabajador'])
            ->oldest('fecha_recogida')
            ->get();

        // Por sede: lo que hay físicamente allí y lo que ha salido con trabajadores
        $sedes = Sede::where('activa', true)
            ->withCount([
                'productos as en_sede_count' => fn ($q) => $q
                    ->where('estado', '!=', 'baja')
                    ->whereDoesntHave('asignacionActiva'),
                'productos as fuera_count' => fn ($q) => $q
                    ->whereHas('asignacionActiva'),
            ])
            ->orderBy('nombre')
            ->get();

        // Por categoría: total (sin bajas) y disponibles
        $categorias = Categoria::withCount([
                'productos as total_count' => fn ($q) => $q->where('estado', '!=', 'baja'),
                'productos as disponibles_count' => fn ($q) => $q->where('estado', 'disponible'),
            ])
            ->orderBy('nombre')
            ->get();

        // Últimos movimientos (asignaciones nuevas o devoluciones)
        $movimientos = Asignacion::with(['producto', 'trabajador', 'usuario'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'diasAlerta', 'porEstado', 'prestamosLargos', 'sedes', 'categorias', 'movimientos'
        ));
    }
}
