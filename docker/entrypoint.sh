#!/bin/bash
set -e

echo "[entrypoint] Aguardando MySQL ficar disponível..."
until php artisan db:monitor --databases=mysql 2>/dev/null; do
  echo "[entrypoint] MySQL ainda não disponível, aguardando..."
  sleep 3
done

echo "[entrypoint] Rodando migrations..."
php artisan migrate --force

echo "[entrypoint] Otimizando a aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] Iniciando PHP-FPM..."
exec "$@"
