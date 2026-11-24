#!/usr/bin/env bash
# One-time project bootstrap.
#
# Generates the Laravel skeleton, installs Filament / Spatie Permission / Inertia / Vue / Tailwind,
# then lays the Phase 2 files from ./overlay on top and removes ./overlay.
# Idempotent: does nothing if the project has already been generated (artisan exists).
#
# Needs network access to packagist.org and registry.npmjs.org (GitHub Actions has it).
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

if [ -f artisan ]; then
  echo "Laravel project already generated; nothing to do."
  exit 0
fi

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_NO_INTERACTION=1
export NO_COLOR=1

echo "==> Creating Laravel skeleton"
TMP="$(mktemp -d)"
composer create-project laravel/laravel "$TMP/app" --no-interaction --prefer-dist --no-ansi
rsync -a --exclude='.git' --exclude='README.md' "$TMP/app/" "$REPO_ROOT/"
rm -rf "$TMP"

echo "==> Laravel version"
php artisan --version --no-ansi

echo "==> Installing PHP packages"
composer require filament/filament spatie/laravel-permission inertiajs/inertia-laravel \
  --with-all-dependencies --no-interaction --no-ansi
for pkg in laravel/framework filament/filament spatie/laravel-permission inertiajs/inertia-laravel; do composer show --no-ansi "$pkg" | grep -E '^(name|versions)'; done

echo "==> Installing JS packages"
npm install --no-audit --no-fund
npm install --save-dev --no-audit --no-fund vue @vitejs/plugin-vue @inertiajs/vue3 tailwindcss @tailwindcss/vite

echo "==> Publishing Filament panel and Spatie Permission migrations"
php artisan filament:install --panels --no-interaction --no-ansi < /dev/null
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction --no-ansi

echo "==> Applying Phase 2 overlay"
cp -a overlay/. .
rm -rf overlay

echo "==> Configuring .env.example (MySQL 8 for real environments; tests use SQLite in memory)"
sed -i 's/^APP_NAME=.*/APP_NAME="AGRI TRADE"/' .env.example
grep -v -E '^#? *DB_' .env.example > .env.example.tmp && mv .env.example.tmp .env.example
cat >> .env.example <<'EOF'

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agri_trade
DB_USERNAME=agri_trade
DB_PASSWORD=

# First Super Admin, created by `php artisan db:seed`. Password must be at least 12 characters.
SUPER_ADMIN_NAME="Super Admin"
SUPER_ADMIN_EMAIL=
SUPER_ADMIN_PASSWORD=
EOF

echo "==> Migrating a scratch SQLite DB so Filament can introspect the users table"
touch database/database.sqlite
DB_CONNECTION=sqlite DB_DATABASE="$REPO_ROOT/database/database.sqlite" php artisan migrate --force --no-interaction --no-ansi
DB_CONNECTION=sqlite DB_DATABASE="$REPO_ROOT/database/database.sqlite" \
  php artisan make:filament-resource User --generate --no-interaction --no-ansi < /dev/null \
  || echo "WARN: could not generate the Filament User resource automatically; add it manually."

echo "==> Bootstrap finished"
