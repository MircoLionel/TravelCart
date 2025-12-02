# TravelCart

TravelCart es una plataforma B2B pensada para que agencias, vendedores y equipos internos gestionen y vendan paquetes turísticos de forma segura, trazable y con workflows aprobatorios. Está construida sobre Laravel 11 con Tailwind + Vite, e incluye autenticación Breeze, panel administrativo y catálogo público listo para operar en entornos demo o productivos.

## ¿Por qué elegir TravelCart?
- **Onboarding inmediato**: base de datos SQLite lista para demos locales y seeds con tours, fechas, cupones y un usuario admin.
- **Control comercial**: aprobación de usuarios antes del checkout, límites de cupos por fecha y cupones con reglas por rol/vigencia.
- **Operación centralizada**: panel admin para gestionar tours, disponibilidades, usuarios y reportes CSV de órdenes.
- **Trazabilidad**: auditoría de cambios sensibles (usuarios, roles) y reportes descargables.
- **Front listo**: assets Vite precompilados y manifest versionado para que un `git clone` muestre la UI estilada sin pasos extra.

## Público objetivo y casos de uso
- **Mayoristas / OTAs internas** que necesitan controlar qué vendedores pueden comprar y con qué condiciones.
- **Equipos comerciales B2B** que requieren catálogos curados, cupos por fecha y cupones con reglas diferenciadas.
- **Demostraciones rápidas**: levantar la app localmente con datos pre-cargados para mostrar el flujo completo en minutos.

## Recorrido rápido del producto
1. **Catálogo público** (`/tours` y `/tours/{tour}`) con destinos, precios, duración e imágenes opcionales.
2. **Carrito y checkout aprobatorio**: usuarios deben ser aprobados antes de confirmar; se valida capacidad y cupones en caliente.
3. **Cupones promocionales**: vigencia, mínimos, roles habilitados y límites de uso por cupón/usuario.
4. **Panel administrativo** (`/admin` con gate `admin`):
   - CRUD de tours y fechas disponibles.
   - Gestión de usuarios (alta, aprobación, rol) con auditoría.
   - Reportes CSV de órdenes.
5. **Notificaciones**: correo de confirmación de compra diferido al commit de la transacción.

## Arquitectura y stack
- **Framework**: Laravel 11 + Breeze (auth), Blade, Eloquent.
- **Frontend**: Tailwind, Vite (assets precompilados en `public/build`).
- **Base de datos**: SQLite para desarrollo inmediato (auto-creación y migración al boot) o MySQL/PostgreSQL.
- **Testing**: suite de features para carrito, checkout, cupones y administración.

## Puesta en marcha rápida
```bash
cp .env.example .env      # o ajusta tus credenciales MySQL existentes
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev               # o npm run build para producción
```

> Si ya cuentas con una base existente (por ejemplo MySQL con datos reales), ajusta las variables `DB_*` en tu `.env` antes de ejecutar las migraciones para mantener la conexión.

Credenciales seed:
- **Admin**: `admin@travelcart.test` / `password`
- **Rol vendedor**: usuarios de ejemplo generados en `DatabaseSeeder`.

## Datos de ejemplo
El `DatabaseSeeder` ejecuta:
- `TourSeeder`: tours con fechas futuras y cupos disponibles.
- `CouponSeeder`: cupones `BIENVENIDA` (10% general) y `MAYORISTA1000` (monto fijo para rol vendor).

Reejecutar seeds:
```bash
php artisan db:seed
```

## Flujos clave
- **Catálogo**: listar/ver tours, filtrables por disponibilidad.
- **Carrito**: agrega fechas de tour, aplica cupones y permite revisar/editar ítems.
- **Checkout**: valida cupos, cupones y confirma orden + correo.
- **Administración**: CRUD de tours/fechas, usuarios, reportes CSV.

## Testing
```bash
php artisan test
```
Cubre auth Breeze, carrito, checkout, cupones y administración. Usa `.env.testing` para valores determinísticos.

## Operación y mantenimiento
- **Migraciones automáticas en SQLite**: si el archivo no existe o faltan tablas clave, el boot crea la base y ejecuta `migrate --seed`.
- **Storage**: ejecutar `php artisan storage:link` para exponer imágenes de tours.
- **Colas**: `php artisan queue:work` si se agregan jobs (p.ej. correos masivos o reportes pesados).

## Roadmap y mejoras propuestas
- **Autoservicio de agencias**: panel de agencias con billing y roles delegados.
- **Pasarela de pago**: integrar Stripe/Mercado Pago con webhooks y reconciliación de órdenes.
- **Inventario avanzado**: reglas de sobreventa controlada y hold de cupos con expiración.
- **CMS de contenidos**: landing builder para destinos y upsells cross-sell.
- **Observabilidad**: métricas de negocio (conversiones por campaña, uso de cupones) y logs centralizados.
- **Internacionalización**: soporte multidioma/multimoneda.
- **Hardening de seguridad**: 2FA opcional, políticas de contraseña y bloqueo por intentos fallidos.

## Estructura de carpetas
| Módulo | Descripción |
| --- | --- |
| `app/Http/Controllers/TourController` | Lista y muestra tours públicos. |
| `app/Http/Controllers/CartController` | Maneja carrito, aplicación de cupones y eliminación de ítems. |
| `app/Http/Controllers/CheckoutController` | Resume el checkout, cierra carritos y crea órdenes. |
| `app/Http/Middleware/EnsureApproved` | Redirige a usuarios no aprobados fuera de los flujos de compra. |
| `app/Http/Controllers/Admin/*` | CRUD de tours/fechas, gestión de usuarios y reportes. |
| `app/Models/*` | Modelos centrales: `Tour`, `TourDate`, `Cart`, `Order`, `Coupon` y `Audit`. |
| `resources/views` | Blade templates para catálogo, carrito, checkout y panel admin. |

## Contribuciones
1. Crea una rama descriptiva: `git checkout -b feature/nueva-funcionalidad`.
2. Asegúrate de que los tests pasen (`php artisan test`).
3. Abre un Pull Request describiendo el cambio y pasos para probarlo.

¡Felices viajes y commits! ✈️
