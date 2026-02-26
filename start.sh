#!/bin/bash
set -e

# Generate .env from environment variables
cat > .env << EOF
APP_NAME="${APP_NAME:-Pembantu.web.id}"
APP_ENV=${APP_ENV:-local}
APP_KEY=
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

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force --no-interaction

# Clear and cache config
php artisan config:clear
php artisan view:clear

# Build assets if not built
if [ ! -f "public/build/manifest.json" ]; then
    npm run build
fi

# Start the Laravel development server on port 5000
php artisan serve --host=0.0.0.0 --port=5000
