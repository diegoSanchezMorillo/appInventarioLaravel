# App de Inventario (Laravel)

Aplicación web para controlar el material de una entidad con varias sedes: ordenadores, proyectores, ratones y cualquier otro equipo. Permite saber en todo momento **qué hay, dónde está y quién lo tiene**.

Desarrollada para uso interno en una entidad con dos sedes, sustituyendo el control manual del material prestado a los trabajadores.

![Panel de inicio](docs/capturas/dashboard.png)

## Funcionalidades

- **Panel de resumen**: productos por estado, material en cada sede y fuera de ella, disponibles por categoría, últimos movimientos y alerta de préstamos temporales que superan los días configurados.
- **Productos**: alta, edición, ficha y baja (borrado lógico). Cada producto recibe una referencia automática según su categoría (`ORD-0001`, `PRY-0002`…). Búsqueda y filtro "¿dónde está?" por sede o trabajador.
- **Asignaciones**: entrega de un producto a un trabajador (temporal o permanente) y registro de la devolución, que puede hacerse en otra sede y cambiar el estado del producto (por ejemplo, a reparación).
- **Sedes, categorías y trabajadores**: CRUD completo. Un trabajador no puede darse de baja mientras tenga material asignado.
- **Usuarios y permisos**: no hay registro público; el administrador crea los usuarios y puede desactivarlos (no se borran). Un usuario desactivado no puede iniciar sesión. Cada usuario edita su propio perfil.
- **Interfaz en español**, con traducciones de Laravel y mensajes de validación propios.

## Tecnologías

| Capa | Tecnología |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Autenticación | laravel/ui (login, recuperación de contraseña), Gate `admin` |
| Base de datos | SQLite por defecto; compatible con MySQL |
| Frontend | Blade, Bootstrap 5, Vite |
| Tests | PHPUnit 12 |

## Decisiones técnicas

- **Lógica de negocio en un servicio** (`app/Services/AsignacionService.php`): asignar y devolver se ejecutan dentro de una transacción con `lockForUpdate()`, para que dos usuarios no puedan entregar el mismo producto a la vez.
- **Validación en Form Requests** (`app/Http/Requests`): por ejemplo, la fecha de recogida no puede ser futura ni la de entrega anterior a la de recogida.
- **Integridad de datos**: claves foráneas con `restrictOnDelete` para no perder el historial, y *soft deletes* en productos y trabajadores.
- **Referencias automáticas**: al crear un producto, el contador de su categoría se incrementa en una transacción y genera la referencia.
- **Scopes y accesores en los modelos** (`abiertas()`, `temporales()`, `enSede()`, `ubicacion`, `estado_texto`) para mantener los controladores sencillos.

## Modelo de datos

```
sedes ──< productos >── categorias
  │          │
  │          └──< asignaciones >── trabajadores >── sedes
  │                    │
  └── sede_devolucion ─┘── users (quién registró el movimiento)
```

Estados de un producto: `disponible`, `asignado`, `reparacion`, `baja`.

## Instalación

Requisitos: PHP 8.3, Composer y Node.js.

```bash
git clone https://github.com/diegoSanchezMorillo/appInventarioLaravel.git
cd appInventarioLaravel/inventario

composer install
npm install

cp .env.example .env
php artisan key:generate

# Base de datos SQLite (por defecto)
touch database/database.sqlite
php artisan migrate --seed

npm run build
php artisan serve
```

La aplicación queda en `http://localhost:8000`. El seeder crea sedes, categorías, trabajadores y productos de ejemplo, además de un usuario administrador (`admin@inventario.test`; la contraseña está en `database/seeders/DatabaseSeeder.php` y conviene cambiarla).

Para usar MySQL, cambia en `.env` las variables `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

### Configuración

| Variable | Valor por defecto | Uso |
| --- | --- | --- |
| `INVENTARIO_DIAS_ALERTA` | `30` | Días a partir de los cuales un préstamo temporal aparece como alerta en el panel |
| `APP_LOCALE` | `en` | Pon `es` para la interfaz en español |

## Tests

```bash
php artisan test
```

`tests/Feature/AsignacionFlowTest.php` cubre el flujo completo de asignación y devolución con 20 casos, entre ellos:

- No se puede asignar un producto dos veces ni uno que esté en reparación.
- Un trabajador dado de baja no aparece en el formulario ni puede recibir material.
- La devolución en otra sede mueve el producto a esa sede y puede marcarlo como averiado.
- No se puede devolver dos veces ni registrar una recogida anterior a la última devolución.
- El filtro "¿dónde está?" distingue entre material en sede y material con un trabajador.
- Las rutas de asignar y devolver exigen haber iniciado sesión.

## Autor

**Diego Sánchez Morillo** · [LinkedIn](https://www.linkedin.com/in/diego-sánchez-morillo-68157a209) · [GitHub](https://github.com/diegoSanchezMorillo)
