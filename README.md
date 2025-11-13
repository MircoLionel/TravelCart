# TravelCart

TravelCart es una plataforma B2B para que agencias y equipos de ventas reserven paquetes turísticos curados por el equipo interno. El proyecto está construido sobre Laravel 11 e incorpora autenticación con Breeze, un flujo de aprobación para usuarios y un panel administrativo para gestionar la oferta.

## Características principales

- **Catálogo público de tours** con información de destino, precio base, duración e imagen opcional.
- **Carrito y checkout** protegidos por aprobación: los usuarios deben ser aprobados antes de poder reservar.
- **Cupones promocionales** con reglas de vigencia, mínimos, roles habilitados y límites de uso.
- **Panel administrativo** para crear/editar tours, gestionar fechas disponibles, administrar usuarios y descargar reportes CSV de órdenes.
- **Auditoría de cambios críticos** (por ejemplo, modificaciones de usuarios) para tener trazabilidad.

## Estructura relevante

| Módulo | Descripción |
| --- | --- |
| `app/Http/Controllers/TourController` | Lista y muestra tours públicos. |
| `app/Http/Controllers/CartController` | Maneja carrito, aplicación de cupones y eliminación de ítems. |
| `app/Http/Controllers/CheckoutController` | Resume el checkout, cierra carritos y crea órdenes. |
| `app/Http/Middleware/EnsureApproved` | Redirige a usuarios no aprobados fuera de los flujos de compra. |
| `app/Http/Controllers/Admin/*` | CRUD de tours/fechas, gestión de usuarios y reportes. |
| `app/Models/*` | Modelos centrales: `Tour`, `TourDate`, `Cart`, `Order`, `Coupon` y `Audit`. |
| `resources/views` | Blade templates para catálogo, carrito, checkout y panel admin. |

## Requisitos previos

- PHP 8.2+
- Composer
- SQLite (para desarrollo rápido) o MySQL/PostgreSQL
- Node.js 18+ (para assets con Vite)

## Puesta en marcha

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev # o npm run build para producción
```

> Si ya cuentas con una base existente (por ejemplo, MySQL con datos reales), ajusta las variables `DB_*` en tu `.env` antes de ejecutar las migraciones para mantener la conexión.

> El seeder genera tours de ejemplo con fechas futuras, cupones y un usuario administrador (`admin@travelcart.test` / `password`) para acceder al panel.

### Tests

Ejecuta la suite completa con:

```bash
php artisan test
```

Los tests de características cubren autenticación básica (Breeze) más flujos críticos de carrito, checkout, cupones y administración.

## Flujos principales

- **Catálogo**: `/tours` y `/tours/{tour}` muestran la oferta disponible.
- **Carrito**: `/cart` permite agregar fechas de tours (`CartController@add`), aplicar cupones y revisar el resumen.
- **Checkout**: `/checkout` confirma la orden y envía correo de confirmación (`CheckoutController@placeOrder`).
- **Administración**: rutas bajo `/admin` (protegidas por gate `admin`) para gestionar tours, fechas y usuarios.

## Seeds y datos de ejemplo

El `DatabaseSeeder` ejecuta:

- `TourSeeder`: crea tours con fechas activas y cupos disponibles.
- `CouponSeeder`: carga cupones `BIENVENIDA` (10% general) y `MAYORISTA1000` (monto fijo para rol vendor).

Puedes ejecutar nuevamente los seeds con:

```bash
php artisan db:seed
```

## Comandos útiles

- `php artisan tinker`: inspección rápida de modelos.
- `php artisan queue:work`: procesar jobs si se agregan correos o reportes asincrónicos.
- `php artisan storage:link`: publicar imágenes de tours cargadas en el panel.

## Contribuciones

1. Crea una rama descriptiva: `git checkout -b feature/nueva-funcionalidad`.
2. Asegúrate de que los tests pasen (`php artisan test`).
3. Abre un Pull Request describiendo el cambio y pasos para probarlo.

¡Felices viajes y commits! ✈️
