#!/usr/bin/env bash
# Sauvegarde MMI'e — base MariaDB + photos
# Usage : bash scripts/backup.sh
# Cron  : 0 2 * * * /bin/bash /var/www/scripts/backup.sh >> /var/log/mmie-backup.log 2>&1

set -euo pipefail

# ── Configuration ──────────────────────────────────────────────────────────────
BACKUP_DIR="${BACKUP_DIR:-/var/backups/mmie}"
APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
TIMESTAMP=$(date +%Y-%m-%d_%H-%M)
RETENTION_DAYS=30

# Variables DB (lues depuis le .env si présent)
if [[ -f "$APP_DIR/.env" ]]; then
    source <(grep -E '^DB_(HOST|PORT|DATABASE|USERNAME|PASSWORD)=' "$APP_DIR/.env" | sed 's/^/export /')
fi

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-mmie}"
DB_USERNAME="${DB_USERNAME:-mmie}"
DB_PASSWORD="${DB_PASSWORD:-}"

PHOTOS_DIR="$APP_DIR/storage/app/private/photos"

# ── Préparation ────────────────────────────────────────────────────────────────
mkdir -p "$BACKUP_DIR"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Début de la sauvegarde"

# ── Base de données ────────────────────────────────────────────────────────────
DB_FILE="$BACKUP_DIR/db_${TIMESTAMP}.sql.gz"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Sauvegarde base de données → $DB_FILE"

mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --password="$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    "$DB_DATABASE" | gzip > "$DB_FILE"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Base sauvegardée ($(du -sh "$DB_FILE" | cut -f1))"

# ── Photos ─────────────────────────────────────────────────────────────────────
if [[ -d "$PHOTOS_DIR" ]]; then
    PHOTOS_FILE="$BACKUP_DIR/photos_${TIMESTAMP}.tar.gz"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Sauvegarde photos → $PHOTOS_FILE"
    tar -czf "$PHOTOS_FILE" -C "$APP_DIR/storage/app/private" photos/
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Photos sauvegardées ($(du -sh "$PHOTOS_FILE" | cut -f1))"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Dossier photos absent, ignoré"
fi

# ── Nettoyage (> 30 jours) ─────────────────────────────────────────────────────
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Nettoyage des sauvegardes de plus de $RETENTION_DAYS jours"
find "$BACKUP_DIR" -name "db_*.sql.gz" -mtime +"$RETENTION_DAYS" -delete
find "$BACKUP_DIR" -name "photos_*.tar.gz" -mtime +"$RETENTION_DAYS" -delete

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Sauvegarde terminée"
