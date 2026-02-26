# Pembantu.web.id

A Laravel 12 marketplace platform for domestic worker services (multi-jasa tenaga kerja) built with Livewire, Tailwind CSS, and PostgreSQL.

## Stack

- **Backend:** PHP 8.4 / Laravel 12
- **Frontend:** Livewire 3.5, Tailwind CSS 3, Vite
- **Database:** PostgreSQL (Replit managed via `DATABASE_URL`)
- **Payments:** Midtrans

## Project Layout

```
app/
  Domain/       # Domain-driven business logic (Escrow, Order, Payment, etc.)
  Livewire/     # Livewire components (Admin, Agency, Auth, Pages, etc.)
  Models/       # Eloquent models
  Http/         # Controllers, Middleware, Requests
config/         # Laravel configuration files
database/
  migrations/   # All DB migrations
resources/
  views/        # Blade templates
  css/app.css   # Tailwind entry
  js/app.js     # JS entry
routes/
  web.php       # Web routes
  api.php       # API routes
public/build/   # Compiled Vite assets
start.sh        # Startup script (generates .env, runs migrations, starts server)
```

## Running Locally

The app is launched via `start.sh` which:
1. Generates `.env` from environment variables
2. Generates the app key if missing
3. Runs database migrations
4. Builds frontend assets if needed
5. Starts `php artisan serve` on `0.0.0.0:5000`

## Environment Variables

Key variables (set as Replit env vars/secrets):
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD` — set automatically by Replit
- `APP_KEY` — generated on first run, stored as a secret
- `APP_NAME`, `APP_ENV`, `APP_URL`, `APP_DEBUG`, `DB_CONNECTION` — set as shared env vars
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY` — optional payment gateway keys

## Admin Panel Routes

- `/admin/dashboard` — Dashboard with overview stats
- `/admin/users` — User management
- `/admin/orders` — Order management with stats, search, status filter
- `/admin/agencies` — Agency management
- `/admin/payout-queue` — Payout processing queue
- `/admin/bank-accounts` — Bank account verification
- `/admin/profile` — Admin profile

## Agency Panel Routes

- `/agency/dashboard` — Agency dashboard
- `/agency/orders` — Agency order management (view only, no create button)
- `/agency/contracts` — Contract queue (review & sign pending contracts)
- `/agency/workers` — Worker management
- `/agency/bank-accounts` — Bank account management (add/verify/set primary)
- `/agency/profile` — Agency profile

## Domain Architecture

The app uses a domain-driven design with the following domains:
- **Auth** — authentication and authorization
- **Order** — order lifecycle management
- **Payment** — payment processing via Midtrans
- **Escrow** — escrow hold management
- **Dispute** — dispute resolution
- **Payout** — worker payouts
- **Refund** — refund processing
- **Ledger** — wallet ledger entries
- **Audit** — audit logging
- **Bank** — bank account management
- **Contract** — service contracts
- **Worker** — worker profiles and verification
