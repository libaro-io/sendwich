# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# Sendwich — Development Conventions

These are the shared PHP & Laravel house rules. They are project-agnostic coding
conventions carried over from our other Laravel projects — follow them for all new
and modified code.

---

## Commands

```bash
# Frontend (run on the host)
npm run dev          # Vite dev server
npm run build        # Production build

# PHP / Artisan (always via the Docker `php` service)
docker compose exec php php artisan migrate
docker compose exec php php artisan tinker
docker compose exec php composer install

# Filament
docker compose exec php php artisan make:filament-resource Order
docker compose exec php php artisan filament:upgrade   # also runs automatically on composer dump-autoload

# Xdebug (composer scripts inside the container)
docker compose exec php composer xdebug:enable
docker compose exec php composer xdebug:disable
```

## Project setup

- **Stack:** Laravel 13 (PHP 8.4) + Filament 5 (admin panel) + Inertia 3 + Vue 3 + Tailwind CSS v4 + Vite. Auth scaffolding via Laravel Breeze (Inertia/Vue stack). Authorization via `spatie/laravel-permission`.
- **PHP runs in Docker** — always prefix Artisan/Composer with `docker compose exec php`. The container runs as user `dev` with the project mounted at `/var/www`.
- **Services** (`docker-compose.yml`): `php` (host port `DOCKER_PHP_PORT`, default `80`) and `mysql` (MySQL 8.4, host port `DOCKER_MYSQL_PORT`, default `3306`).
- **Database:** MySQL, `DB_HOST=mysql`; database/user/password all default to `dev` locally.
- **Queue:** `QUEUE_CONNECTION=sync` locally (jobs run synchronously).
- The **Filament admin panel** lives at `/admin` (panel id `admin`), runs in SPA mode, and auto-discovers Resources, Pages, and Widgets under `app/Filament/`.

---

## PHP Conventions

### Brackets
Always use curly braces, even for single-line blocks.

```php
// Bad
if ($foo === 'bar')
    $response = true;

// Good
if ($foo === 'bar') {
    $response = true;
}
```

### Variable naming
Always use full, descriptive variable names. Never abbreviate.

```php
// Bad
$p = $config->polygon;
foreach ($properties as $p) {}

// Good
$polygon = $config->polygon;
foreach ($properties as $property) {}
```

### Strict comparison
Always use `===` / `!==`.

```php
// Bad
if ($string == 'description') {}

// Good
if ($string === 'description') {}
```

### Return types
Always declare return types where possible.

```php
public function getUser(int $id): User
{
    return User::query()->findOrFail($id);
}
```

### Type hints
Always type-hint parameters.

```php
// Bad
public function calculateProfit($start, $end) {}

// Good
public function calculateProfit(float $start, float $end): float {}
```

### Nullable parameters at the end
```php
// Bad
public function doThing(string $method, $nullable = null, User $user) {}

// Good
public function doThing(string $method, User $user, $nullable = null) {}
```

### Fluent setters for optional parameters
Avoid long constructor signatures with many nullables — use fluent setters instead.

```php
public function setOrganizer(User $organizer): self
{
    $this->organizer = $organizer;
    return $this;
}
```

### Happy path
Handle exceptions/edge cases early, keep the happy path at the bottom.

```php
// Bad
if ($crucialThing) {
    // Do things
}
throw new Exception;

// Good
if (!$crucialThing) {
    throw new Exception;
}
// Do things
```

### No useless else after return
```php
// Bad
if ($yay) {
    return 'yay';
} else {
    return 'ney';
}

// Good
if ($yay) {
    return 'yay';
}
return 'ney';
```

### DocBlocks
Only add DocBlocks when they add real value — non-obvious parameters, thrown exceptions, or complex return types. Never document the obvious.

```php
// Bad — useless
/** @param $view */
public function index($view) {}

// Good — adds value
/**
 * @param string $view  The name of the view to use as a template.
 * @throws ViewNotFoundException
 * @return Response
 */
public function index(string $view): Response {}
```

### Custom exceptions
Use custom exceptions instead of the base `Exception` class so callers can catch them specifically.

---

## Laravel Conventions

### Eloquent `where()` — always include the operator
```php
// Bad
Model::query()->where('column', 'value')->get();

// Good
Model::query()->where('column', '=', 'value')->get();
```

### FormRequests

Always use a dedicated `FormRequest` class for validation — never call `$request->validate()` inside a controller.

- **`authorize()`** handles ownership and permission checks. Replaces manual `if (...) abort(403)` in the controller.
- **`rules()`** contains all validation rules, including DB-dependent checks (e.g. max count based on existing records).
- DB queries inside a `FormRequest` use `once()` to avoid duplicate queries when the same data is needed in both `rules()` and a public getter.
- Expose computed data to the controller via **public getter methods** on the `FormRequest`.

```php
// Bad — validation and authorization in the controller
public function store(Request $request, Property $property): JsonResponse
{
    $request->validate(['images' => 'required|array']);
    if ($property->user_id !== Auth::id()) {
        abort(403);
    }
}

// Good — controller is clean, FormRequest handles everything
public function store(StorePropertyImage $request, Property $property): JsonResponse
{
    $existingCount = $request->getExistingImageCount();
}
```

```php
class StorePropertyImage extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');
        return $property->user_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', function ($attribute, $value, $fail) {
                if ($this->getExistingImageCount() + count($value) > 10) {
                    $fail('Maximum exceeded.');
                }
            }],
        ];
    }

    public function getExistingImageCount(): int
    {
        return once(fn () => $this->route('property')->images()->count());
    }
}
```

Use `Rule::enum(MyEnum::class)` to validate enum values:

```php
'status' => ['required', Rule::enum(PublicationRequestStatus::class)],
```

### Migrations

- Use migrations for all database changes.
- Always implement `down()` for rollback.
- For **risky migrations** (data mutations, drops, renames): require a `down()` and, where feasible, export the affected data (e.g. to CSV) before mutating.

### Routes & Controllers

- URLs are always plural and optionally nested: `/matches/{id}/events`
- Controller methods follow REST: `index`, `create`, `store`, `show`, `update`, `destroy`
- Use fully qualified class names in route files (IDE refactor support):

```php
// Bad
Route::get('/matches', 'MatchController@showMatches');

// Good
Route::get('/matches', [MatchController::class, 'index']);
```

- Avoid ad-hoc methods like `MatchController->updateEvent()` — create a dedicated `MatchEventController` instead.

### No PHP logic in Blade
There is never a reason to add PHP to a Blade file beyond simple output. Blade templates must not contain `@php` blocks or business/computation logic. Prepare all data in the controller (or a dedicated view model) and pass it ready-to-render; Blade only echoes variables and uses control directives (`@if`, `@foreach`, `@include`).

```blade
{{-- Bad — logic computed in the view --}}
@php
    $displayImage = $property->approvedImages->first()?->url ?? $property->image1;
@endphp

{{-- Good — controller prepares the value, Blade just outputs it --}}
{{ $displayImage }}
```

---

## Filament Conventions

The admin panel is built with **Filament 5**. Resources, Pages, and Widgets are
auto-discovered from `app/Filament/` — never wire them up manually in the panel
provider.

### Where things live
- **Resources** → `app/Filament/Resources/{Model}Resource.php`, with page classes under `{Model}Resource/Pages/` (`List`, `Create`, `Edit`) and relation managers under `{Model}Resource/RelationManagers/`. Generate with `make:filament-resource`, don't hand-roll the directory layout.
- **Custom pages** → `app/Filament/Pages/`.
- **Widgets** (stats, charts) → `app/Filament/Widgets/`.

### Conventions
- One Resource per Eloquent model. Keep schema definitions in `form()`/`table()` (or dedicated schema classes for Filament 5) — do not move Filament form logic into the model.
- Authorization goes through **Policies** (`app/Policies/`) and `spatie/laravel-permission`, not ad-hoc checks inside the Resource.
- Keep heavy/business logic out of Resources — delegate to Actions (`app/Actions/`), Jobs, or services and call them from the Resource.
- The panel runs in SPA mode (`->spa()`); brand colour is orange. Match the existing panel config in `app/Providers/Filament/AdminPanelProvider.php` rather than introducing a second panel.

---

## Inertia / Vue Conventions

The user-facing app is **Inertia 3 + Vue 3**, bootstrapped in `resources/js/app.js`.
There is no separate REST/SPA frontend — controllers return Inertia responses that
render Vue pages.

### Structure
- **Pages** → `resources/js/Pages/` (resolved by name from the controller's `Inertia::render('Dashboard')`). Subfolders mirror feature areas (`Pages/Store/`, `Pages/Auth/`, `Pages/Legal/`).
- **Reusable components** → `resources/js/Components/`.
- **Layouts** → `resources/js/Layouts/` (`Authenticated.vue`, `Guest.vue`, `Landing.vue`).
- **Composables** → `resources/js/Composables/`.

### Conventions
- Controllers return **`Inertia::render('PageName', [...])`** — prepare all data server-side and pass it as props. Apply the same "no logic in the view" rule to Vue templates: keep computation in the controller or in `computed`/composables, not inline in the template.
- Use **Ziggy** for route URLs in JS — call `route('store.show', id)` instead of hardcoding paths. Route names must stay in sync with `routes/web.php`.
- Use **`laravel-permission-to-vuejs`** for permission checks in Vue (`can`/`is`) instead of duplicating permission logic. Server-side authorization still goes through `can:` middleware / Policies — the frontend check is UX only.
- Use the shared `Toast` (vue-toastification) and the global `emitter` (mitt, available as `this.emitter` / `globalProperties.emitter`) that are already registered in `app.js` — don't spin up new instances.
- Tailwind CSS v4 (via `@tailwindcss/vite`) + daisyUI. Style with utility classes; reach for daisyUI components before writing custom CSS.

### Routes & middleware (Inertia side)
- Page routes live in `routes/web.php`, API/JSON endpoints in `routes/api.php`.
- Protect authenticated pages with `['auth', 'verified']` and gate features with Spatie permissions via `can:` middleware (e.g. `can:edit-store`, `can:edit-company`), matching the existing routes.
- Single-purpose endpoints use **single-action `__invoke` controllers** (e.g. `ShowSettingsController`, `SaveRunnerSettingsController`) — follow that pattern instead of adding unrelated methods to a fat controller. This is the same principle as the Routes & Controllers rule above.
