#!/bin/bash

mkdir -p bootstrap/cache storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs

EXISTING_KEY=""
if [ -f .env ]; then
    EXISTING_KEY=$(grep '^APP_KEY=' .env | head -1 | cut -d= -f2-)
fi

cat > .env << EOF
APP_NAME="${APP_NAME:-Pembantu.web.id}"
APP_ENV=${APP_ENV:-local}
APP_KEY=${EXISTING_KEY:-${APP_KEY:-}}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${APP_URL:-http://localhost:5000}
APP_TIMEZONE=${APP_TIMEZONE:-Asia/Jakarta}
APP_LOCALE=${APP_LOCALE:-id}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${PGHOST:-127.0.0.1}
DB_PORT=${PGPORT:-5432}
DB_DATABASE=${PGDATABASE:-pembantu_web_id}
DB_USERNAME=${PGUSER:-postgres}
DB_PASSWORD=${PGPASSWORD:-}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=hello@pembantu.web.id
MAIL_FROM_NAME="${APP_NAME:-Pembantu.web.id}"

MIDTRANS_SERVER_KEY=${MIDTRANS_SERVER_KEY:-}
MIDTRANS_CLIENT_KEY=${MIDTRANS_CLIENT_KEY:-}
MIDTRANS_IS_PRODUCTION=${MIDTRANS_IS_PRODUCTION:-false}
MIDTRANS_API_URL=${MIDTRANS_API_URL:-https://app.midtrans.com}
MIDTRANS_API_SANDBOX_URL=${MIDTRANS_API_SANDBOX_URL:-https://app.sandbox.midtrans.com}
MIDTRANS_CALLBACK_URL=${APP_URL:-http://localhost:5000}/api/payment/callback

ENABLE_EMAIL_NOTIFICATIONS=${ENABLE_EMAIL_NOTIFICATIONS:-false}
ENABLE_SMS_NOTIFICATIONS=${ENABLE_SMS_NOTIFICATIONS:-false}
ENABLE_MESSAGING=${ENABLE_MESSAGING:-false}
EOF

if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

php artisan migrate --force --no-interaction 2>&1 || true

php artisan config:clear 2>&1
php artisan view:clear 2>&1

if [ ! -f "public/build/manifest.json" ]; then
    npm run build
fi

exec php artisan serve --host=0.0.0.0 --port=5000
