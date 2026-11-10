<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Sede;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checklist de asignar / devolver convertido en tests.
 * Usa SQLite en memoria (phpunit.xml): no toca la base de datos real.
 */
class AsignacionFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Sede $sedeA;
    private Sede $sedeB;
    private Categoria $categoria;
    private Producto $producto;
    private Trabajador $trabajador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = User::factory()->create();
        $this->sedeA = Sede::create(['nombre' => 'Sede Madrid', 'activa' => true]);
        $this->sedeB = Sede::create(['nombre' => 'Sede Sevilla', 'activa' => true]);
        $this->categoria = Categoria::create(['nombre' => 'Ordenadores', 'prefijo' => 'ORD']);

        $this->producto = $this->crearProducto();
        $this->trabajador = $this->crearTrabajador('Ana', 'García Pérez', '11111111A');
    }

    private function crearProducto(array $extra = []): Producto
    {
        return Producto::create($extra + [
            'nombre' => 'Portátil', 'categoria_id' => $this->categoria->id,
            'sede_id' => $this->sedeA->id, 'estado' => 'disponible',
        ]);
    }

    private function crearTrabajador(string $nombre, string $apellidos, string $dni): Trabajador
    {
        return Trabajador::create([
            'nombre' => $nombre, 'apellidos' => $apellidos, 'dni' => $dni,
            'sede_id' => $this->sedeA->id, 'activo' => true,
        ]);
    }

    /** Asigna por la ruta real (la misma que usa el formulario). */
    private function asignar(Producto $p, Trabajador $t, string $fecha, string $tipo = 'temporal')
    {
        return $this->actingAs($this->user)->post(route('productos.asignar.store', $p), [
            'trabajador_id' => $t->id, 'tipo' => $tipo, 'fecha_recogida' => $fecha,
        ]);
    }

    private function asignarYObtener(?string $fecha = null): Asignacion
    {
        $this->asignar($this->producto, $this->trabajador, $fecha ?? now()->toDateString());

        return Asignacion::latest('id')->first();
    }

    /* =========================== ASIGNAR =========================== */

    public function test_01_producto_disponible_muestra_boton_asignar(): void
    {
        $this->actingAs($this->user)->get(route('productos.show', $this->producto))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('Asignar a trabajador');
    }

    public function test_02_sin_trabajador_da_error_en_el_campo(): void
    {
        $this->actingAs($this->user)
            ->from(route('productos.asignar', $this->producto))
            ->post(route('productos.asignar.store', $this->producto), [
                'tipo' => 'temporal', 'fecha_recogida' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('trabajador_id');

        $this->assertDatabaseCount('asignaciones', 0);
    }

    public function test_03_fecha_de_recogida_de_manana_es_rechazada(): void
    {
        $this->actingAs($this->user)->post(route('productos.asignar.store', $this->producto), [
            'trabajador_id' => $this->trabajador->id, 'tipo' => 'temporal',
            'fecha_recogida' => now()->addDay()->toDateString(),
        ])->assertSessionHasErrors('fecha_recogida');

        $this->assertDatabaseCount('asignaciones', 0);
    }

    public function test_04_y_05_asignacion_correcta_y_fila_en_base_de_datos(): void
    {
        $respuesta = $this->asignar($this->producto, $this->trabajador, now()->toDateString());

        $respuesta->assertRedirect(route('productos.show', $this->producto));
        $respuesta->assertSessionHas('success', 'ORD-0001 asignado a Ana García Pérez.');

        $this->actingAs($this->user)->get(route('productos.show', $this->producto))
            ->assertSee('Asignado')
            ->assertSee('alert-primary', false)
            ->assertSee('Ana García Pérez')
            ->assertSee('table-primary', false)
            ->assertSee('En su poder');

        $fila = Asignacion::latest('id')->first();
        $this->assertSame($this->user->id, $fila->user_id);
        $this->assertNull($fila->fecha_entrega);
        $this->assertSame('asignado', $this->producto->fresh()->estado);
    }

    public function test_06_no_se_puede_asignar_dos_veces(): void
    {
        $this->asignar($this->producto, $this->trabajador, now()->toDateString());

        $this->actingAs($this->user)->get("/productos/{$this->producto->id}/asignar")
            ->assertRedirect(route('productos.show', $this->producto))
            ->assertSessionHas('error', 'No se puede asignar: el producto está "Asignado".');

        $this->asignar($this->producto, $this->trabajador, now()->toDateString())
            ->assertSessionHas('error');
        $this->assertDatabaseCount('asignaciones', 1);
    }

    public function test_07_producto_en_reparacion_no_es_asignable(): void
    {
        $rep = $this->crearProducto(['estado' => 'reparacion', 'nombre' => 'Roto']);

        $this->actingAs($this->user)->get(route('productos.show', $rep))
            ->assertSee('No asignable: En reparación')
            ->assertDontSee('Asignar a trabajador');
    }

    public function test_08_trabajador_dado_de_baja_no_aparece_ni_se_puede_asignar(): void
    {
        $baja = $this->crearTrabajador('Luis', 'Baja Test', '22222222B');
        $this->actingAs($this->user)->delete(route('trabajadores.destroy', $baja));

        $this->actingAs($this->user)->get(route('productos.asignar', $this->producto))
            ->assertOk()
            ->assertSee('García Pérez')
            ->assertDontSee('Baja Test');

        $this->asignar($this->producto, $baja, now()->toDateString())
            ->assertSessionHasErrors('trabajador_id');
    }

    /* =========================== DEVOLVER =========================== */

    public function test_09_formulario_devolver_muestra_quien_lo_tiene_y_sede_preseleccionada(): void
    {
        $a = $this->asignarYObtener(now()->subDays(3)->toDateString());

        $this->actingAs($this->user)->get(route('asignaciones.devolver', $a))
            ->assertOk()
            ->assertSee('Ana García Pérez')
            ->assertSee(now()->subDays(3)->format('d/m/Y'))
            ->assertSeeInOrder(['value="' . $this->sedeA->id . '"', 'selected'], false);
    }

    public function test_10_fecha_entrega_anterior_a_recogida_da_error(): void
    {
        $a = $this->asignarYObtener(now()->subDays(3)->toDateString());

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->subDays(5)->toDateString(),
            'sede_devolucion_id' => $this->sedeA->id, 'estado' => 'disponible',
        ])->assertSessionHasErrors(['fecha_entrega' => 'La fecha de entrega no puede ser anterior a la de recogida.']);

        $this->assertNull($a->fresh()->fecha_entrega);
    }

    public function test_11_y_12_devolucion_en_otra_sede_con_averia(): void
    {
        $this->actingAs($this->user)->post(route('productos.asignar.store', $this->producto), [
            'trabajador_id' => $this->trabajador->id, 'tipo' => 'temporal',
            'fecha_recogida' => now()->subDays(3)->toDateString(),
            'observaciones' => 'Con cargador',
        ]);
        $a = Asignacion::latest('id')->first();

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->toDateString(),
            'sede_devolucion_id' => $this->sedeB->id,
            'estado' => 'reparacion',
            'observaciones' => 'Pantalla rota',
        ])->assertRedirect(route('productos.show', $this->producto));

        $this->actingAs($this->user)->get(route('productos.show', $this->producto))
            ->assertSee('En reparación')
            ->assertSee('Sede Sevilla')
            ->assertSee(now()->format('d/m/Y'));

        $a->refresh();
        $this->assertNotNull($a->fecha_entrega);
        $this->assertSame($this->sedeB->id, $a->sede_devolucion_id);
        $this->assertSame("Con cargador\nDevolución: Pantalla rota", $a->observaciones);

        $p = $this->producto->fresh();
        $this->assertSame('reparacion', $p->estado);
        $this->assertSame($this->sedeB->id, $p->sede_id);
    }

    public function test_13_no_se_puede_devolver_dos_veces(): void
    {
        $a = $this->asignarYObtener(now()->subDay()->toDateString());
        $datos = [
            'fecha_entrega' => now()->toDateString(),
            'sede_devolucion_id' => $this->sedeA->id, 'estado' => 'disponible',
        ];
        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), $datos);

        $this->actingAs($this->user)->get("/asignaciones/{$a->id}/devolver")
            ->assertRedirect(route('productos.show', $this->producto))
            ->assertSessionHas('error', 'Esta devolución ya estaba registrada.');

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), $datos)
            ->assertSessionHas('error', 'Esta devolución ya estaba registrada.');
    }

    /* ===================== SOLAPAMIENTO DE FECHAS ===================== */

    public function test_14_y_15_no_se_puede_recoger_antes_de_la_ultima_devolucion(): void
    {
        $a = $this->asignarYObtener(now()->subDays(10)->toDateString());
        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->subDays(2)->toDateString(),
            'sede_devolucion_id' => $this->sedeB->id, 'estado' => 'reparacion',
        ]);

        $this->actingAs($this->user)->put(route('productos.update', $this->producto), [
            'nombre' => 'Portátil', 'sede_id' => $this->sedeB->id, 'estado' => 'disponible',
        ])->assertRedirect();
        $this->assertSame('disponible', $this->producto->fresh()->estado);

        $this->asignar($this->producto, $this->trabajador, now()->subDays(5)->toDateString())
            ->assertSessionHasErrors(['fecha_recogida' => 'La fecha de recogida no puede ser anterior a la última devolución de este producto.']);

        $this->asignar($this->producto, $this->trabajador, now()->toDateString())
            ->assertSessionHas('success');
        $this->assertSame(2, $this->producto->asignaciones()->count());

        $this->actingAs($this->user)->get(route('productos.show', $this->producto))
            ->assertSeeInOrder([now()->format('d/m/Y'), now()->subDays(10)->format('d/m/Y')]);
    }

    /* ================ DESDE LA FICHA DEL TRABAJADOR ================ */

    public function test_16_y_17_devolver_desde_la_ficha_del_trabajador(): void
    {
        $a = $this->asignarYObtener(now()->subDays(2)->toDateString());

        $this->actingAs($this->user)->get(route('trabajadores.show', $this->trabajador))
            ->assertOk()
            ->assertSee('Devolver')
            ->assertSee(route('asignaciones.devolver', $a), false)
            ->assertSee(route('productos.show', $this->producto), false);

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->toDateString(),
            'sede_devolucion_id' => $this->sedeA->id, 'estado' => 'disponible',
        ])->assertRedirect(route('productos.show', $this->producto));

        $this->actingAs($this->user)->get(route('trabajadores.show', $this->trabajador))
            ->assertSee('No tiene ningún producto ahora mismo.')
            ->assertSee('ORD-0001');
    }

    /* ======================= COHERENCIA GENERAL ======================= */

    public function test_18_filtro_donde_esta(): void
    {
        $a = $this->asignarYObtener(now()->subDays(2)->toDateString());

        $this->actingAs($this->user)->get(route('productos.index', ['ubicacion' => 't-' . $this->trabajador->id]))
            ->assertSee('ORD-0001');
        $this->actingAs($this->user)->get(route('productos.index', ['ubicacion' => 's-' . $this->sedeB->id]))
            ->assertDontSee('ORD-0001');

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->toDateString(),
            'sede_devolucion_id' => $this->sedeB->id, 'estado' => 'disponible',
        ]);

        $this->actingAs($this->user)->get(route('productos.index', ['ubicacion' => 's-' . $this->sedeB->id]))
            ->assertSee('ORD-0001');
        $this->actingAs($this->user)->get(route('productos.index', ['ubicacion' => 't-' . $this->trabajador->id]))
            ->assertDontSee('ORD-0001');
    }

    public function test_19_baja_de_trabajador_bloqueada_hasta_devolver_todo(): void
    {
        $a = $this->asignarYObtener(now()->subDays(2)->toDateString());

        $this->actingAs($this->user)->delete(route('trabajadores.destroy', $this->trabajador))
            ->assertSessionHas('error');
        $this->assertNull($this->trabajador->fresh()->deleted_at);

        $this->actingAs($this->user)->patch(route('asignaciones.devolver.store', $a), [
            'fecha_entrega' => now()->toDateString(),
            'sede_devolucion_id' => $this->sedeA->id, 'estado' => 'disponible',
        ]);

        $this->actingAs($this->user)->delete(route('trabajadores.destroy', $this->trabajador))
            ->assertRedirect(route('trabajadores.index'))
            ->assertSessionHas('success');
        $this->assertSoftDeleted('trabajadores', ['id' => $this->trabajador->id]);
    }

    /* ======================== SEGURIDAD ======================== */

    public function test_20_las_rutas_de_asignar_y_devolver_exigen_login(): void
    {
        $a = $this->asignarYObtener();
        auth()->logout();

        $this->get(route('productos.asignar', $this->producto))->assertRedirect(route('login'));
        $this->post(route('productos.asignar.store', $this->producto), [])->assertRedirect(route('login'));
        $this->get(route('asignaciones.devolver', $a))->assertRedirect(route('login'));
        $this->patch(route('asignaciones.devolver.store', $a), [])->assertRedirect(route('login'));
    }
}
