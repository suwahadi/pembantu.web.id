#!/bin/bash

mkdir -p bootstrap/cache storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs

EXISTING_KEY=""
if [ -f .env ]; then
    EXISTING_KEY=$(grep '^APP_KEY=' .env | head -1 | cut -d= -f2-)
fi

if [ -n "$REPLIT_DEV_DOMAIN" ]; then
    RESOLVED_APP_URL="https://${REPLIT_DEV_DOMAIN}"
else
    RESOLVED_APP_URL="http://localhost:5000"
fi

cat > .env << ENVEOF
APP_NAME="Pembantu.web.id"
APP_ENV=local
APP_KEY=${EXISTING_KEY}
APP_DEBUG=true
APP_URL=${RESOLVED_APP_URL}
ASSET_URL=${RESOLVED_APP_URL}
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=hello@pembantu.web.id
MAIL_FROM_NAME="Pembantu.web.id"

MIDTRANS_SERVER_KEY=${MIDTRANS_SERVER_KEY}
MIDTRANS_CLIENT_KEY=${MIDTRANS_CLIENT_KEY}
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_API_URL=https://app.midtrans.com
MIDTRANS_API_SANDBOX_URL=https://app.sandbox.midtrans.com
MIDTRANS_CALLBACK_URL=${RESOLVED_APP_URL}/api/payment/callback

ENABLE_EMAIL_NOTIFICATIONS=false
ENABLE_SMS_NOTIFICATIONS=false
ENABLE_MESSAGING=false
ENVEOF

if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

php artisan migrate --force --no-interaction 2>&1 || true

php artisan config:clear 2>&1
php artisan view:clear 2>&1
php artisan route:clear 2>&1

if [ ! -f "public/build/manifest.json" ]; then
    npm run build
fi

while true; do
    echo "[$(date)] Starting PHP server on port 5000..."
    php artisan serve --host=0.0.0.0 --port=5000
    EXIT_CODE=$?
    echo "[$(date)] Server exited with code $EXIT_CODE, restarting in 2 seconds..."
    sleep 2
done
