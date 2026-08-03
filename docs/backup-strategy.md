# PURE Research Hub: Backup & Restore Strategy

This document outlines the official procedure for backing up and restoring the PURE Research Hub production environment. The platform consists of two critical data components: the relational database (MySQL) and the private storage directory (which contains sensitive un-sanitized PDFs and thumbnails).

## 1. Automated Scheduled Backups

It is highly recommended to configure a cron job on the production server to automate the backup process nightly.

### Example Cron Job Setup
Run `crontab -e` and add the following to run the backup script daily at 2:00 AM:
```bash
0 2 * * * /path/to/pure-research-hub/scripts/backup.sh >> /var/log/pure_backup.log 2>&1
```

## 2. Database Backup (MySQL)

The database contains user accounts, research metadata, relationships, and system configurations.

### Creating a Backup
Use `mysqldump` from the host machine or from within the Docker container.
```bash
# Assuming Docker container name is 'pure-db-1'
docker exec pure-db-1 /usr/bin/mysqldump -u root --password=your_root_password pure_database > /path/to/backups/db_backup_$(date +%F).sql
```
*Note: Ensure you compress the SQL file (e.g., using `gzip`) if storage space is a concern.*

### Restoring the Database
**Warning:** This will overwrite the current database!
```bash
docker exec -i pure-db-1 /usr/bin/mysql -u root --password=your_root_password pure_database < /path/to/backups/db_backup_2026-08-02.sql
```

## 3. Private Storage Backup

The `storage/app/private` directory contains the raw research PDFs uploaded by users, thumbnails, and other sensitive materials.

### Creating a Backup
Use `tar` to archive the storage directory.
```bash
# Run this from the root of your Laravel project
tar -czvf /path/to/backups/storage_backup_$(date +%F).tar.gz storage/app/private/
```

### Restoring Private Storage
**Warning:** Always ensure correct ownership and permissions after extracting files.
```bash
# Extract the archive back into the project
tar -xzvf /path/to/backups/storage_backup_2026-08-02.tar.gz -C /path/to/pure-research-hub/

# Ensure the web server or PHP-FPM user owns the restored files (e.g., www-data)
chown -R www-data:www-data /path/to/pure-research-hub/storage/app/private
chmod -R 775 /path/to/pure-research-hub/storage/app/private
```

## 4. Disaster Recovery (Full Restore)

If migrating to a new server or recovering from a total failure:
1. Provision the new server and clone the repository.
2. Copy the `.env` file containing your encryption keys (`APP_KEY`) and database credentials. **This is critical, as passwords and sessions depend on the `APP_KEY`**.
3. Run `docker-compose up -d --build` to start the infrastructure.
4. Restore the database using the instructions in Section 2.
5. Restore the private storage using the instructions in Section 3.
6. Run `docker-compose exec app php artisan optimize:clear` to flush stale cache.
7. Verify functionality by accessing the `/health` endpoint.
