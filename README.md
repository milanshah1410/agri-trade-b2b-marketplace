# AGRI TRADE – All India B2B Agriculture Marketplace

B2B platform for bulk agricultural commodities: catalogue, pricing, inventory, checkout, Razorpay payments,
third-party freight, shipment tracking and an admin back office.

Stack: Laravel, PHP 8.3, MySQL 8, Vue 3 + Inertia + Tailwind, Filament (admin), Spatie Permission, Redis queue.
Design docs: [`docs/PHASE1_ARCHITECTURE.md`](docs/PHASE1_ARCHITECTURE.md).

## Status

| Phase | Scope | State |
|---|---|---|
| 1 | Architecture, ER diagrams, plan | Done |
| 2 | Laravel setup, auth, roles and permissions, Filament panel | In progress (see CI) |
| 3–9 | Catalogue, pricing, checkout, orders, logistics, dashboards, hardening | Not started |

## First-time project generation

The Laravel skeleton and dependencies are produced by `scripts/bootstrap.sh`, which GitHub Actions runs on the
first push (it commits the generated project back to the branch when all tests pass). To do it locally instead:

```bash
bash scripts/bootstrap.sh
```

## Run locally

```bash
cp .env.example .env
# set DB_PASSWORD, SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD (12+ characters) in .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm ci && npm run build   # or: npm run dev
```

Open http://localhost:8000. Staff sign in at `/admin`; customers register at `/register`.

Without Docker: PHP 8.3+, Composer, Node 22, a MySQL 8 database named `agri_trade`, then the same artisan/npm commands.

## Roles

`super-admin`, `admin`, `sales-manager`, `warehouse-manager` (admin panel); `customer`, `transporter`, `driver`
(portals). Permissions live in `app/Support/PermissionCatalog.php`; `php artisan db:seed --class=RolesAndPermissionsSeeder`
is safe to re-run. Public registration only ever creates customers.

## Tests

```bash
php artisan test
```

Tests run on in-memory SQLite. Phase 4 adds concurrency tests that need MySQL.

## Secrets

Never commit `.env`. Razorpay and Google Maps keys are server-side settings only.
GST rates and HSN codes must be entered and verified with your CA before production.
