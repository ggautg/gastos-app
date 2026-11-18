# Gastos App

Aplicación para registrar gastos y ganancias en guaraníes, organizarlos por categorías, seguir presupuestos y compartir el mismo hogar con otras personas. La interfaz está íntegramente en español (rioplatense) y funciona con MySQL.

## Stack

- **Backend:** Laravel 13, PHP 8.3, Eloquent, Inertia 2.
- **Frontend:** Vue 3 (`<script setup>`), Tailwind 4, Chart.js, Lucide, Vite 8.
- **Base de datos:** MySQL 8 (`gastos_app`). Los tests usan SQLite en memoria.
- **Reportes:** DomPDF (PDF) y Maatwebsite Excel (XLSX).
- **Idioma:** `es` / `es_PY`, con mensajes del framework en `lang/es/`.

## Puesta en marcha

Requisitos: PHP 8.3 con extensiones de Laravel, Composer, Node 20+ y MySQL 8 en ejecución.

```bash
# 1) Crear la base de datos una sola vez
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS gastos_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# Contraseña de root en este entorno: ionysmaster666

# 2) Dependencias
composer install
npm install

# 3) Configuración
cp .env.example .env
php artisan key:generate

# 4) Base de datos y assets
php artisan migrate
npm run build

# 5) Servidores de desarrollo (en dos terminales)
php artisan serve --host=127.0.0.1 --port=8000
npm run dev -- --host 127.0.0.1
```

`.env` y `.env.example` ya apuntan a MySQL con `DB_DATABASE=gastos_app`, `DB_USERNAME=root` y `DB_PASSWORD="ionysmaster666"`. No hace falta tocar nada más para levantar la app en este equipo.

## Modelo de datos

| Tabla | Para qué sirve |
|---|---|
| `users` | Personas. Guarda `current_household_id`, el hogar que se está viendo ahora. |
| `households` | Hogar: nombre, código de invitación y dueño. |
| `household_user` | Relación muchos a muchos con el rol: `owner` o `viewer`. |
| `categories` | Categorías del hogar: nombre, tipo (`gasto`/`ganancia`), color, ícono y presupuesto mensual. |
| `transactions` | Movimientos: tipo, monto, monto en guaraníes, moneda, descripción, fecha, categoría y usuario. |

Relaciones importantes:

- Todo se consulta **dentro del hogar activo** (`users.current_household_id`). Cambiar de hogar no mezcla datos.
- Un `viewer` puede mirar, pero no crear, editar ni borrar.
- `transactions.user_id` es nullable: al eliminar una cuenta, sus movimientos quedan con `user_id = null` y no se pierden.
- `transactions.category_id` usa `RESTRICT`: no se puede borrar una categoría con movimientos. Hay guardas equivalentes para SQLite.
- Si el dueño sale o elimina su cuenta, otro integrante pasa a ser `owner`.
- Al eliminar un hogar se reubica a sus integrantes y se borran primero sus movimientos y categorías, para respetar la FK `RESTRICT` de MySQL.

## Dinero y fechas

- Los importes se guardan dos veces: `amount` (valor original) y `amount_gs` (equivalente en guaraníes). Las sumas y los gráficos usan siempre `amount_gs`.
- El formato visual es `₲ 150.000` (`es-PY`). Está centralizado en `resources/js/Composables/useFormat.js`; ningún componente arma el formato por su cuenta.
- Los inputs de monto muestran miles pero envían solo dígitos al servidor.
- Las fechas se envían como `YYYY-MM-DD` y se muestran como `dd/mm/aaaa`. El selector de mes/año calcula los años alrededor del actual en `useYears.js`.
- La zona horaria de la aplicación es UTC (`config/app.php`).

## Rutas principales

Todas las rutas autenticadas pasan por `auth` + `verified`.

| Ruta | Controlador | Función |
|---|---|---|
| `GET /dashboard` | `DashboardController@index` | Panel con balance, resumen diario y accesos. |
| `GET/POST/PUT/DELETE /transactions` | `TransactionController` | Alta, edición, borrado y listado por mes. |
| `GET /transactions/weekly` | `WeeklySummaryController@index` | Comparativa semana por semana. |
| `GET /transactions/export/excel` / `/pdf` | `TransactionController` | Exportaciones del mes. |
| `GET/POST/PUT/DELETE /categories` | `CategoryController` | Categorías y presupuestos. |
| `GET /insights` | `InsightsController@index` | Comparativa contra el mes anterior. |
| `GET /summary/yearly` | `YearlySummaryController@index` | Resumen anual. |
| `GET /household` | `HouseholdController@show` | Hogar, invitaciones, integrantes y cambio de hogar. |
| `POST /household/join` / `/switch` | `HouseholdController` | Unirse con código / cambiar de hogar. |
| `PATCH /household` | `HouseholdController@update` | Renombrar el hogar (solo `owner`). |
| `DELETE /household` / `/household/leave` | `HouseholdController` | Eliminar el hogar / salir de él. |

## Organización del frontend

- `resources/js/Pages`: una página por ruta Inertia.
- `resources/js/Components`: piezas reutilizables. Las bases comunes son:
  - `AppModal.vue`: fondo, panel, título, cierre con Escape, foco inicial, trap de Tab y bloqueo de scroll. Lo usan los modales de movimientos, categorías, borrado y eliminación de cuenta.
  - `PageHeader.vue`: etiqueta + título de página.
  - `EmptyState.vue`: estado vacío con mensaje, detalle y acción.
  - `ToastHost.vue`: el único lugar que pinta el toast global.
  - `MonthYearSelector.vue` y `FiltersBar.vue`: selects de mes/año con años dinámicos.
- `resources/js/Composables`: lógica compartida.
  - `useFormat.js`: moneda, números compactos y fechas.
  - `useToast.js`: toast global con reinicio del temporizador.
  - `useYears.js`: años disponibles en los selectores.
  - `useTheme.js`, `useChartColors.js`, `useBackdropClose.js`: tema, colores de gráficos y cierre de modales.
- `resources/css/app-theme.css`: los seis temas (`bosque`, `noche`, `mandioca`, `terere`, `verano`, `ueno`). Cada tema redeclara todas las variables derivadas para no heredar valores de otro. `app.css` usa tokens como `--cs-on-teal`, `--cs-on-terra`, `--cs-on-gold` y `--cs-progress` para mantener contraste en temas claros y oscuros.

## Tests

```bash
php artisan test
npm run build
```

Los tests de feature están en `tests/Feature` y corren contra SQLite `:memory:` (ver `phpunit.xml`), así que nunca tocan la MySQL de desarrollo. Cubren aislamiento por hogar, permisos, invitaciones, cambio de hogar, borrado de cuenta y eliminación de hogares con movimientos.

## Cambios de esta revisión

1. **Idioma:** `APP_LOCALE=es`, `lang/es/{auth,passwords,pagination,validation}.php`, textos de validación y auth en español, y corrección de términos (`Dashboard` → `Panel`, `Email` → `Correo electrónico`, `Cuenta compartida` → `Mi hogar`, `Borrar` → `Eliminar`, `link` → `enlace`, `preset` → `tema`).
2. **Backend:** mensajes de error accionables y con el mismo vocabulario que la UI. Arreglado el borrado de hogares con movimientos: ahora se limpian movimientos antes que categorías para no chocar con `RESTRICT`.
3. **Temas:** tokens de contraste por acento y variables derivadas redeclaradas en los seis presets; reemplazo de blancos y RGBA fijos por tokens; barra de progreso de Inertia con el color del tema.
4. **Componentización:** `AppModal`, `PageHeader`, `EmptyState`, `ToastHost`, `useFormat`, `useYears`; migración de modales, páginas, estados vacíos y formato de guaraníes. Se borraron componentes sin uso (`Modal`, `NavLink`, `ResponsiveNavLink`, `SecondaryButton`, `DangerButton`).
5. **UI/UX:** modales accesibles (`role="dialog"`, `aria-modal`, Escape, foco, Tab), años dinámicos, estados vacíos con acción, toast único y con el tono correcto, y menos duplicación de markup.
6. **Documentación:** este README y docblocks en español en modelos, controladores, requests, middleware y provider.

## Convenciones

- Todo el texto visible va en español rioplatense (voseo: «cargá», «elegí», «podés»).
- Los mensajes de error dicen qué pasó y qué hacer después.
- La lógica repetida va a un composable; el markup repetido va a un componente.
- Los modelos y controladores llevan docblocks que explican cada método; lo mismo vale para requests, middleware y provider.
- No se usa service worker: se dio de baja en `resources/js/app.js` para evitar cachés viejas.

---

Hecho sin commits ni push: los cambios quedan en el árbol de trabajo para revisión.
