# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Last verified against the codebase: 2026-06-05.

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

# Vue scaffolding — @libaro-io/laravel-frontend-conventions (run on the host)
npx libaro-create -c -n order-card   # new component + matching CSS file (-p page, -s section, -l layout)
npx libaro-rename                    # interactive rename: renames the Vue + CSS pair and updates selectors/imports

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
- **Database:** MySQL; database/user/password all default to `dev` locally. Inside the Docker network the host is the service name `mysql` (`.env.example` ships `DB_HOST=127.0.0.1` for host-side access).
- **Queue:** `QUEUE_CONNECTION=sync` locally (jobs run synchronously).
- The **Filament admin panel** lives at `/admin` (panel id `admin`), runs in SPA mode (brand colour orange), and auto-discovers Resources, Pages, and Widgets under `app/Filament/` (Resources exist for Order, User, Store, Product, Company).
- **TypeScript is being introduced on the frontend**: strict `tsconfig.json` with `allowJs` (existing `.js`/JS-SFCs stay as they are), shared Inertia page-prop types in `resources/js/types.d.ts`, and newly scaffolded Vue files use `<script setup lang="ts">`.
- **Vite/TS aliases**: `@` → `resources/js` (jsconfig + tsconfig), `@css` → `resources/css`, `@components` and `@layouts` (vite.config.js). `tsconfig.json` declares more (`@composables`, `@interfaces`, `@actions`, `@pages`, `@enums`) — those extra ones are TS-only, so use them for **type-only imports** unless they are added to vite.config.js. NB: the alias targets are **lowercase** (`resources/js/layouts`) while the existing folders are capitalised (`resources/js/Layouts`) — identical on macOS, different on case-sensitive filesystems.
- **Libaro tooling**: `libaro-io.config.ts` (project root) configures `@libaro-io/laravel-frontend-conventions`; `tailwindReferencePlugin()` from `@libaro-io/libaro-utilities` is registered in `vite.config.js` and auto-injects `@reference "@css/app.css";` into every SFC `<style>` block. `package.json` carries an `overrides` block that maps `@libaro-io/libaro-utilities`'s vite peer to the project's Vite 8 — don't remove it, or `npm install` fails with ERESOLVE.
- **Backend layout**: besides the usual Laravel dirs, `app/` holds `Actions/`, `Casts/`, `DataObjects/`, `Enums/`, `Exceptions/`, `Jobs/`, `Mail/`, `Notifications/`, `Policies/`, `Templates/`, and `View/`. Controllers are single-action and grouped per feature (`Controllers/Company/`, `Controllers/History/`, `Controllers/Order/`, `Controllers/Store/`).

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
- **Reusable components** → `resources/js/Components/`, grouped by role:
  - `Components/ui/` — design-system widgets reused across pages: `Modal` (Headless UI), `Dropdown`, `DropdownLink`, and the Breeze form primitives `Input`, `Label`, `Checkbox`, `InputError`, `ValidationErrors`, `Title`.
  - `Components/layout/` — app-shell pieces used by the `Authenticated`/`Guest` layouts: `ApplicationLogo`, `NavLink`, `ResponsiveNavLink`, `FlashMessages`.
  - `Components/frontend/` — shared marketing chrome: `Navigation`, `Footer` (Legal pages + `Authenticated` layout).
- **Only genuinely reusable Vue files belong in `Components/`.** A Vue file that is merely a *section* of one page lives next to its page: `Pages/<Page>/sections/<Section>.vue` (+ matching CSS under `resources/css/pages/<page>/sections/`). This mirrors `libaro-create -s`. Current section sets: `Pages/Dashboard/sections/` (`Orders`, `Products`, `ProductCard`, `Menu`, `DeptList`, `PayBack`), `Pages/History/sections/HistoryTable.vue`, `Pages/Display/sections/` (`UsersWithOrders`, `SelectedRunner`), `Pages/Store/sections/` (`List`, `ListItem`), `Pages/Homepage/sections/`.
- **Layouts** → `resources/js/Layouts/` (`Authenticated.vue`, `Guest.vue`, `Landing.vue` — the latter only used by the Legal pages).
- **Composables** → `resources/js/Composables/`; **directives** → `resources/js/Directives/` (e.g. the `v-reveal` scroll-reveal in `reveal.ts`).
- **Shared Inertia prop types** → `resources/js/types.d.ts` (augments `@inertiajs/core` `PageProps` and exposes Ziggy's `route()` to TS templates); page-specific interfaces under `resources/js/interfaces/` (e.g. `homepage.ts`).
- **Marketing landing page** → `Pages/Welcome.vue` (thin composition page + `resources/css/pages/homepage.css` shell), served by `HomepageController` at `/` (route name `home`; authenticated users are redirected to the dashboard). Its sections live under `Pages/Homepage/sections/` (`Navigation`, `Hero`, `Complaints`, `OrderTicket`, `Perks`, `Steps`, `CallToAction`, `Footer`), each with a matching stylesheet under `resources/css/pages/homepage/sections/`.

### Scaffolding Vue files — `@libaro-io/laravel-frontend-conventions`

New Vue files are scaffolded with the `libaro-create` CLI — don't hand-roll the file pair. Renaming goes through `libaro-rename`, never by hand.

- `npx libaro-create -c|-p|-s|-l -n <name>` creates a **c**omponent, **p**age, **s**ection or **l**ayout. Anything not passed as a flag is asked interactively; the target-folder picker is *always* interactive, so the user has to run this command in a terminal themselves (Claude: ask the user to run it, e.g. via the `!` prefix, then build on the generated files).
- Each run generates a **pair of files**: a kebab-case `.vue` file under `resources/js/<type>/…` and a matching kebab-case stylesheet under `resources/css/<same path>/…`, imported in the SFC's `<style scoped>` via the `@css` alias (Tailwind v4) or `@scss` (v3).
- The generated root element carries a selector derived from type + folder path + name — keep it on the root element and put the styles inside that block in the matching CSS file:
  - components & sections → `<section class="component-<path>-<name>-component">` + a `.class` block;
  - pages & layouts → `<div id="page-<path>-<name>">` + an `#id` block.
- Config lives in **`libaro-io.config.ts`** at the project root: `tailwindVersion: 4`, `componentSuffix: '-component'`, `useLibaroVitePlugin: true`.
- The supporting Vite wiring is in place in `vite.config.js`: the `@css` → `resources/css` alias, and `tailwindReferencePlugin()` from `@libaro-io/libaro-utilities`, which auto-injects `@reference "@css/app.css";` into every SFC `<style>` block — so `@apply` works in scoped styles **without** adding a manual `@reference` line. (`@libaro-io/libaro-utilities` also ships a `translationEnforcer()` Vite plugin for typed `getTrans` helpers from `lang/`; it is not registered yet.)

### Conventions
- Controllers return **`Inertia::render('PageName', [...])`** — prepare all data server-side and pass it as props. Apply the same "no logic in the view" rule to Vue templates: keep computation in the controller or in `computed`/composables, not inline in the template.
- Use **Ziggy** for route URLs in JS — call `route('store.show', id)` instead of hardcoding paths. Route names must stay in sync with `routes/web.php`.
- Use **`laravel-permission-to-vuejs`** for permission checks in Vue (`can`/`is`) instead of duplicating permission logic. Server-side authorization still goes through `can:` middleware / Policies — the frontend check is UX only.
- Use the shared `Toast` (vue-toastification) and the global `emitter` (mitt, available as `this.emitter` / `globalProperties.emitter`) that are already registered in `app.js` — don't spin up new instances.
- Icons come from **Font Awesome** (`@fortawesome/vue-fontawesome`): register icons in the `library` in `app.js`, import `FontAwesomeIcon` locally, and use string names (`icon="fa-solid fa-trash"`). Never hand-roll inline SVGs.
- Tailwind CSS v4 (via `@tailwindcss/vite`). **daisyUI has been completely removed and uninstalled** — the app is entirely custom-styled.
- **Styling lives in CSS, not in templates.** A `.vue` file's markup carries **only defined class names** (design-system classes or per-file semantic classes) — never bare Tailwind utilities like `flex`, `mt-4`, `text-ink`. Every utility is defined inside a class via `@apply`, in one of:
  - the **design system** in `resources/css/app.css` (shared, app-wide primitives), or
  - a **per-file CSS pair** co-located by path and imported in the SFC's `<style scoped>` via `@import "@css/<same path>.css";` — e.g. `Components/Orders.vue` ↔ `resources/css/components/orders.css`, `Pages/Settings.vue` ↔ `resources/css/pages/settings.css`. (`tailwindReferencePlugin` auto-injects `@reference`, so `@apply` works; standalone CSS files still start with `@reference "@css/app.css";`.)
  - **Gotcha:** content rendered through a teleport/portal (e.g. inside `ui/Modal.vue`) is not reached by descendant selectors — use **flat** BEM classes (`.orders__weight-input`, not `.orders .weight-input`) so scoped styling still applies.
- **Reference design:** `Pages/Homepage.vue` (+ `Pages/Homepage/sections/*`) and the design-system block of `app.css`. Brand tokens live in the `@theme` block (`cream`, `paper`, `ink`, `ink-soft`, `teal(-deep/-soft/-ink)`, `coral(-soft)`, `sun(-soft)`, `font-display` Fredoka, `font-script` Caveat) and are available as utilities (`bg-cream`, `text-ink`, `font-display`, …) — use those inside `@apply`, never raw hex.
- **App-wide primitives in `app.css`** (the daisyUI replacements):
  - buttons → `.chunk` (+ `--teal`/`--cream`/`--coral`/`--sun`/`--ghost`/`--sm`/`--lg`, `:disabled`)
  - surface → `.panel` (+ `--flat`); section headers → `.sec-head`/`.sec-tab(--teal)`/`.sec-title`/`.sec-sub`; `.panel-title`
  - status → `.tag` (+ `--teal`/`--coral`/`--sun`/`--ink`/`--bold`/`--semibold`); tints → `.tint-teal`/`-coral`/`-sun`
  - forms → `.field-label`/`.field-input`/`.field-select`/`.field-textarea`/`.field-error`/`.field-checkbox`; form scaffolding → `.form-field`/`.form-actions(--end)`/`.form-link(--danger)`/`.form-status`/`.form-hint`/`.form-prose`/`.form-check(-label)`/`.form-title(--spaced)`
  - toggle → `.switch`; table → `.table-brut`; inline notice → `.callout` (+ `--warning`/`--error`/`--info`)
  - nav → `.nav-link(-active)`/`.nav-link-mobile(-active)`/`.dropdown-link`
  - page shell → `.app-page`/`.page`/`.page-container`; helpers → `.icon-btn`(`--danger`/`__icon`), `.empty-action`, `.reveal` (paired with the `v-reveal` directive)
  - behavioural components → `ui/Modal.vue` (Headless UI dialog), `Components/Dropdown.vue`
  - toast styling → the `.Vue-Toastification__toast*` overrides in `app.css`

### Routes & middleware (Inertia side)
- Page routes live in `routes/web.php`, API/JSON endpoints in `routes/api.php`.
- Protect authenticated pages with `['auth', 'verified']` and gate features with Spatie permissions via `can:` middleware (e.g. `can:edit-store`, `can:edit-company`), matching the existing routes.
- Single-purpose endpoints use **single-action `__invoke` controllers** (e.g. `ShowSettingsController`, `SaveRunnerSettingsController`) — follow that pattern instead of adding unrelated methods to a fat controller. This is the same principle as the Routes & Controllers rule above.
