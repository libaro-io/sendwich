# Sendwich — App-wide Restyle Plan

> Self-contained execution plan. Goal: restyle the entire user-facing app to the
> design language of the new landing page (`resources/js/Pages/Homepage.vue`) and
> **remove daisyUI completely** at the end. Work phase by phase; every phase must
> end with a passing `npm run build` and a usable app.

---

## 1. Project context (read this first)

- **Stack**: Laravel 13 (PHP 8.4, runs in Docker — prefix artisan/composer with
  `docker compose exec php`), Inertia 3 + Vue 3, Tailwind CSS **v4** via
  `@tailwindcss/vite`, TypeScript being introduced (`tsconfig.json`, strict, `allowJs`).
  Frontend commands run on the host: `npm run dev` / `npm run build`.
- **Read `CLAUDE.md`** in the repo root — it contains the house rules. Key ones:
  - Style with Tailwind utility classes; design tokens live in the `@theme` block of
    `resources/css/app.css` and are used as utilities (`bg-cream`, `text-ink`, `font-display`, …).
  - **daisyUI is legacy**: never use daisyUI classes in touched code. It stays installed
    until Phase 4 so unmigrated pages keep working.
  - A Vue file used by exactly **one** page is a *section* and lives at
    `Pages/<Page>/sections/<Section>.vue` with matching CSS under
    `resources/css/pages/<page>/sections/`. Only genuinely reusable components live in
    `resources/js/Components/`.
  - Icons: Font Awesome via npm (`@fortawesome/vue-fontawesome`); register icons in the
    `library` in `resources/js/app.js`, import `FontAwesomeIcon` locally. **No hand-rolled inline SVGs.**
  - New Vue files use `<script setup lang="ts">`.
  - `tailwindReferencePlugin()` (in `vite.config.js`) auto-injects
    `@reference "@css/app.css";` into every SFC `<style>` block, so `@apply` works in
    scoped styles without a manual `@reference` line. Standalone CSS files (the
    `resources/css/**` pairs) DO start with `@reference "@css/app.css";`.
- **Reference design (do not modify)**: `Pages/Homepage.vue` + `Pages/Homepage/sections/*`
  + `resources/css/pages/homepage.css` + `resources/css/pages/homepage/sections/*.css`.
- **Admin is out of scope**: everything under `app/Filament` keeps its Filament styling.

### Design tokens (already defined in `resources/css/app.css` `@theme`)

| Token | Value | Use |
|---|---|---|
| `cream` | `#fdf5e8` | page background |
| `paper` | `#fffaf0` | card/surface background |
| `ink` | `#2a2724` | primary text & borders |
| `ink-soft` | `#6f655a` | secondary text |
| `teal` / `teal-deep` / `teal-soft` / `teal-ink` | `#2dbe9c` / `#1f9a7e` / `#d2f3ea` / `#06251e` | primary accent / accent text / tint / text-on-teal |
| `coral` / `coral-soft` | `#ff6f59` / `#ffe1da` | danger & secondary accent |
| `sun` / `sun-soft` | `#ffc23c` / `#fff0cf` | highlight accent |
| `font-display` | Fredoka | headings, buttons, labels |
| `font-script` | Caveat | playful accents |

### Primitives that already exist globally in `app.css`

- `.chunk` button (+ `--teal`, `--cream`, `--sm`, `--lg`) — Fredoka, 3px ink border,
  hard offset shadow, hover `-translate` + bigger shadow.
- `.sec-head` / `.sec-tab` (+ `--teal`) / `.sec-title` / `.sec-sub` — section headers.
- `.tint-teal` / `.tint-coral` / `.tint-sun` — soft tint backgrounds.
- `.reveal` (+ `.is-in`) — scroll-reveal, paired with the `v-reveal` directive in
  `resources/js/Directives/reveal.ts`.

### Design language — the rules for every screen

| Element | Style |
|---|---|
| Page background | `bg-cream`; content sits on paper panels |
| Surface/card | `.panel` (new): `bg-paper border-[3px] border-ink rounded-2xl shadow-[6px_6px_0_var(--color-ink)] p-6` |
| Buttons | `.chunk` family; add `--coral` (danger), `--ghost` (outline), `:disabled` |
| Status/labels | `.tag` (new) pill: Fredoka, `border-2 border-ink`, variants `--teal/--coral/--sun/--ink` |
| Form fields | `.field-label` (Fredoka semibold), `.field-input`/`-select`/`-textarea` (paper bg, `border-2 border-ink rounded-lg`, focus → teal border + ring), `.field-error` (coral) |
| Toggle | `.switch` (new, replaces daisyUI `toggle`) |
| Tables | `.table-brut` (new): Fredoka header, ink lines, row hover `bg-cream` |
| Inline notices | `.callout` (new, replaces daisyUI `alert`) + tint variants |
| Toasts | vue-toastification CSS overrides: teal success / coral error / ink info, ink border + hard shadow |
| Typography | Fredoka (`font-display`) for headings/buttons/labels, Nunito body, `text-ink` / `text-ink-soft` |
| Motion | hover = `-translate-x-0.5 -translate-y-0.5` + larger shadow; respect `prefers-reduced-motion` |

**Naming guard**: while daisyUI is still installed its global component classes leak onto
elements. Never name new classes `card`, `badge`, `btn`, `toggle`, `alert`, `table`,
`menu`, `steps`, `step`, `hero`, `footer`, `tab`, `avatar`, `list`, `label`, `loading`.
That's why the new primitives are `.panel`, `.tag`, `.switch`, `.callout`, `.table-brut`.

### Gotchas

- Tailwind v4 syntax: `text-(--ink)` style var-shorthands are NOT used here — use the
  token utilities (`text-ink`). Arbitrary values like `shadow-[6px_6px_0_var(--color-ink)]` are fine.
- In scoped styles / CSS pairs, keep `animation:` declarations and `@keyframes` as raw
  CSS (Vue's scoped compiler rewrites keyframe names; `@apply` for those breaks the rename).
- `app.css` currently has global `h1/h2/h3 { @apply ... text-primary mb-5 }` rules
  (daisyUI-era). They get REMOVED in Phase 4 — every migrated page must style its own
  headings explicitly (color + margin + size), don't rely on those globals.

---

## 2. Decided architecture

1. **Modals**: one reusable `Components/ui/Modal.vue` built on `@headlessui/vue`
   `Dialog` (+`TransitionRoot`). The package is already in `package.json`, unused.
   Replaces all 34 daisyUI checkbox-toggle modals.
2. **Hybrid UI kit**: CSS primitives for stateless things (`.chunk`, `.panel`, `.tag`,
   `.field*`, `.switch`, `.table-brut`, `.callout`); Vue components only where behaviour
   lives (`ui/Modal.vue`; restyle the existing `Dropdown.vue` and Breeze form components
   instead of replacing them).
3. **Phased rollout** on branch `fix/new-style`; daisyUI removal is the last phase.

---

## 3. Phases

### Phase 0 — Foundation

1. `resources/views/app.blade.php`:
   - Extend the Google Fonts link with Fredoka (500;600;700) and Caveat (600;700)
     (Nunito is already there). Then delete the per-page font `<link>`s from the
     `<Head>` of `Pages/Homepage.vue`.
   - Remove `data-theme="cupcake"` from `<html>`.
   - Remove the `kit.fontawesome.com` `<script>` (icons come from the npm packages).
2. `resources/css/app.css` — add to the existing "Sendwich design system" block
   (all via `@apply` on tokens):

   ```css
   /* surface */
   .panel { @apply bg-paper border-[3px] border-ink rounded-2xl shadow-[6px_6px_0_var(--color-ink)] p-6; }
   .panel--flat { @apply shadow-[3px_3px_0_var(--color-ink)] p-4; }

   /* extra button variants */
   .chunk--coral { @apply bg-coral text-paper; }
   .chunk--ghost { @apply bg-transparent; }
   .chunk:disabled { @apply opacity-50 pointer-events-none; }

   /* status tag (daisyUI `badge` replacement) */
   .tag { @apply inline-flex items-center gap-1 font-display text-[0.85rem] border-2 border-ink rounded-full px-3 py-0.5 bg-paper text-ink; }
   .tag--teal { @apply bg-teal-soft; } .tag--coral { @apply bg-coral-soft; }
   .tag--sun { @apply bg-sun-soft; } .tag--ink { @apply bg-ink text-paper; }

   /* form fields */
   .field-label { @apply block font-display font-semibold text-ink mb-1; }
   .field-input, .field-select, .field-textarea {
       @apply w-full bg-paper text-ink border-2 border-ink rounded-lg px-3 py-2
           focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal-soft;
   }
   .field-error { @apply text-coral text-sm mt-1; }

   /* toggle (daisyUI `toggle` replacement) — checkbox styled as a switch */
   .switch { @apply relative inline-block w-12 h-7 appearance-none cursor-pointer rounded-full
       border-2 border-ink bg-cream transition duration-150 checked:bg-teal; }
   .switch::before { @apply content-[''] absolute top-0.5 left-0.5 w-4 h-4 rounded-full
       bg-paper border-2 border-ink transition duration-150; }
   .switch:checked::before { @apply translate-x-5; }

   /* table */
   .table-brut { @apply w-full border-collapse; }
   .table-brut th { @apply font-display font-semibold text-left text-ink border-b-[3px] border-ink px-3 py-2; }
   .table-brut td { @apply border-b border-ink/20 px-3 py-2; }
   .table-brut tbody tr:hover { @apply bg-cream; }

   /* inline notice (daisyUI `alert` replacement) */
   .callout { @apply flex items-center gap-3 border-2 border-ink rounded-xl px-4 py-3 bg-paper font-semibold; }
   .callout--warning { @apply bg-sun-soft; } .callout--error { @apply bg-coral-soft; }
   .callout--info { @apply bg-teal-soft; }

   /* toast overrides (vue-toastification) */
   .Vue-Toastification__toast { @apply font-sans font-semibold border-[3px] border-ink rounded-xl shadow-[4px_4px_0_var(--color-ink)]; }
   .Vue-Toastification__toast--success { @apply bg-teal text-teal-ink; }
   .Vue-Toastification__toast--error { @apply bg-coral text-paper; }
   .Vue-Toastification__toast--info { @apply bg-ink text-paper; }
   ```
   (Tune values while implementing; keep tokens-only — no hex codes.)
3. New `resources/js/Components/ui/Modal.vue` (`lang="ts"`), Headless UI Dialog:
   props `open: boolean`, `title?: string`; emits `close`; slots default + `#actions`;
   panel = `.panel` styling + Fredoka title; backdrop `bg-ink/40`; transitions; ESC/backdrop close.
4. Restyle the Breeze form components in place (same API, new classes):
   `Components/Input.vue`, `Label.vue`, `Checkbox.vue`, `InputError.vue`,
   `ValidationErrors.vue`, `Title.vue` → use `.field*` (replace the indigo focus styling).

### Phase 1 — Layouts & shared shell

1. `Layouts/Authenticated.vue`: replace the `.bg-nav` green gradient with a paper navbar
   (`bg-paper border-b-[3px] border-ink`), `bg-cream` content background.
   `Components/NavLink.vue` + `ResponsiveNavLink.vue`: active state teal (Fredoka, border-b
   accents) instead of indigo. `Components/Dropdown.vue` + `DropdownLink.vue`: paper menu,
   ink border, hard shadow.
2. `Components/frontend/Footer.vue` (shared by layout + Legal pages): restyle to
   `bg-ink text-paper`, Fredoka labels; remove the daisyUI `footer` class.
3. `Layouts/Guest.vue`: replace `bg-rainbow` with `bg-cream` + a centered `.panel`
   holding the logo + slot. All 8 Auth pages inherit this automatically.
4. Legal pages (`Pages/Legal/{Privacy,Cookies,General}.vue`) +
   `Components/frontend/Navigation.vue`: drop the dark-gradient Landing look; cream
   background, `.sec-title`-style typography, remove all `dark:` variants. Then delete
   `Layouts/Landing.vue`.

### Phase 2 — Core app (dashboard cluster)

Mechanical per file: `card*`→`.panel`, `btn*`→`.chunk*`, `badge*`→`.tag*`,
checkbox-toggle modals→`ui/Modal.vue`, `table`→`.table-brut`, `input*`/`select*`/`textarea*`→`.field*`,
`alert*`→`.callout*`, hardcoded colors→tokens, inline SVGs→Font Awesome.

1. `Pages/Dashboard.vue` + `Components/Orders.vue` (222 lines, heaviest: modal+table+badges),
   `Components/Products.vue` + `Components/Products/productCard.vue`,
   `Components/Menu.vue` + `Components/Stores/storeCard.vue`,
   `Components/DeptList.vue` + `Components/PayBack.vue`.
2. `Pages/History.vue` + `Components/History.vue` (table with inline selects/inputs, `btn-xs`→`.chunk--sm`).
3. `Pages/Display.vue` + `Components/UsersWithOrders.vue`, `SelectedRunner.vue`
   (`bg-white-fix`/`bg-white-50` → `bg-paper`/`bg-paper/70`), `List.vue`, `ListItem.vue`.

### Phase 3 — Form-heavy pages + Auth

1. `Pages/Settings.vue` (366 lines; biggest single job: 25 btn, 13 input, 10 toggle→`.switch`,
   10 modal→`ui/Modal`, 8 select, 7 chat bubbles→simple `.panel--flat` rows, 2 alert→`.callout`, 1 divider→`border-t-2 border-ink`).
2. `Pages/Company.vue` (9 modal, 9 input, 7 toggle, 6 btn, table),
   `Pages/Store/Stores.vue` (uses `.action-button` — replace with `.chunk chunk--teal`),
   `Pages/Store/NewStore.vue` (modal form), `Pages/Store/Products.vue` (16 inputs, 8 selects).
3. Auth pages (`Pages/Auth/*` — 8 files): mostly free via Phases 0–1; per page: submit
   buttons → `.chunk chunk--teal`, links → `text-teal-deep`, check spacing.

### Phase 4 — Remove daisyUI + cleanup

1. `resources/css/app.css`: delete both `@plugin "daisyui"` blocks (plugin + `sendwich`
   theme); delete daisyUI-dependent helpers `.action-button`, `.btn-success`,
   `.card-body`, `.modal-box`; delete the global `h1/h2/h3` rules (verify every page
   styles its own headings first); delete `.bg-rainbow`, the `.bg-white` opacity
   override and `.bg-white-fix` after their users are migrated.
2. `npm uninstall daisyui`.
3. Delete dead code: `Pages/Welcome.vue` (unreachable), `Components/Button.vue` and
   `Components/DoneOrders.vue` (unused), `Layouts/Landing.vue`,
   `Components/FlashMessages.vue` if fully replaced by toasts.
4. Update `CLAUDE.md`: daisyUI sections → "removed"; document the new primitives
   (`.panel`, `.tag`, `.field*`, `.switch`, `.table-brut`, `.callout`, `ui/Modal.vue`);
   drop the class-name-collision gotcha.

---

## 4. Verification (every phase)

1. `npm run build` — must pass with zero errors.
2. Smoke-test the touched routes in the browser (app runs on Docker, port 80):
   `/`, `/dashboard`, `/history`, `/display`, `/company`, `/company/settings`,
   `/stores`, `/store/{id}`, `/login`, `/register`, password-reset flow, `/privacy`.
3. Functional checks: modals open/close (ESC + backdrop), toggles persist, forms submit
   + validation errors render in coral, toasts appear in brand style, mobile nav works,
   `prefers-reduced-motion` honoured.

Final acceptance (after Phase 4):
1. `grep -rE 'class="[^"]*\\b(btn|card|modal|badge|toggle|alert|divider|chat|input-bordered|select-bordered|form-control|label-text)\\b' resources/js` → **zero hits**.
2. `npm run build` passes; compiled app CSS is significantly smaller (daisyUI gone).
3. `docker compose exec php php artisan route:list` passes; visit every route.
4. `CLAUDE.md` matches the end state.