# PURE Research Hub - Production Deployment Guide

This document outlines the production deployment process using Docker, ensuring that the Laravel 13 academic publishing platform operates securely with large file support (up to 128MB).

## 1. Prerequisites
- Docker Engine & Docker Compose
- Domain name mapped to server IP

## 2. Environment Setup

Clone the repository and set up the production environment file:
```bash
git clone https://github.com/your-org/pure-research-hub.git
cd pure-research-hub
cp .env.production.example .env
```

Edit the `.env` file to match your production environment:
- Set `APP_KEY` (generate one if needed via `php artisan key:generate` later)
- Configure `DB_PASSWORD` and database credentials
- Configure `MAIL_HOST` for outbound notifications

## 3. Docker Build and Startup

Build the production containers and start them in detached mode:
```bash
docker compose build
docker compose up -d
```

## 4. Initialization Commands

Execute the following commands inside the `app` container to finalize the deployment:

```bash
# Generate application key if missing
docker compose exec app php artisan key:generate

# Run database migrations
docker compose exec app php artisan migrate --force

# Create storage symlink
docker compose exec app php artisan storage:link

# Cache configuration, routes, and views for production performance
docker compose exec app php artisan optimize
```

## 5. Storage Permissions

The Docker entrypoint automatically ensures `/var/www/storage` and `/var/www/bootstrap/cache` are owned by `www-data`. If you encounter permission issues from the host, verify volumes:
```bash
docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
```

## 6. Queue Worker Configuration

The queue worker is handled by a dedicated `queue` container. It runs `supervisor` with the `queue.conf` automatically retrying failed jobs and limiting processing to 300 seconds.

To check worker logs:
```bash
docker compose exec queue tail -f /var/www/storage/logs/worker.log
```
To restart the queue after application updates:
```bash
docker compose exec queue php artisan queue:restart
```

## 7. Backup Strategy

It is recommended to run a daily cron job on the host machine to backup the MySQL volume and private research files:
```bash
# Backup Database
docker compose exec db sh -c 'exec mysqldump pure_research_hub -upure_user -p"$MYSQL_PASSWORD"' > /backups/db-$(date +%F).sql

# Backup Private Storage
tar -czvf /backups/research-pdfs-$(date +%F).tar.gz ./storage/app/private/research/
```

## 8. Health Checking

You can monitor the platform's health status via the built-in endpoint:
`GET https://your-domain.com/health`

This endpoint returns a 200 JSON response confirming Application, Database, and Queue connectivity.
