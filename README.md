# Enterprise Bulk Email System

A production-ready bulk email system built with Laravel, Vue/Blade, and AWS SES.

## Features
- **Role-Based Access Control**: Super Admin, Admin, Manager, User
- **Campaign Management**: Draft, Approval Workflow, Queue-based Sending
- **Bulk Sending**: Optimized for AWS SES with Redis queues
- **Recipient Management**: CSV Import causing de-duplication
- **Auditing**: Full action logging

## Prerequisites
- Docker & Docker Compose
- AWS SES Credentials (API Key & Secret) with `ses:SendRawEmail` permissions.

## Local Development Setup

1. **Clone & Configure**
   ```bash
   cp .env.example .env
   # Update AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION
   ```

2. **Start Docker**
   ```bash
   docker-compose up -d --build
   ```

3. **Install Dependencies & Initialize**
   ```bash
   # Enter the container
   docker-compose exec app bash
   
   # Run Composer
   composer install
   
   # Generate Key
   php artisan key:generate
   
   # Run Migrations
   php artisan migrate --seed
   
   # Link Storage
   php artisan storage:link
   ```

4. **Access**
   - Web: `http://localhost:8080`
   - **Default Admin Login**:
     - Email: `admin@example.com`
     - Password: `password`
   - Database: Port 3306
   - Redis: Port 6379

## Deployment Guide: CPanel (Shared/VPS)

This system is designed to run in Docker, but can be adapted for CPanel environments that support PHP 8.2+ and SSH access.

### 1. Database Setup
1.  Log in to CPanel > **MySQL Databases**.
2.  Create a new database (e.g., `cpuser_bulkemail`).
3.  Create a user and assign **ALL PRIVILEGES** to the database.
4.  Note down the credentials.

### 2. File Upload
1.  Zip your project files (excluding `vendor` and `node_modules`).
2.  In CPanel **File Manager**, upload to a private folder (e.g., `/home/cpuser/bulk-email-app`).
    *   *Do NOT put the entire app in `public_html` for security.*
3.  Unzip the files.

### 3. Public Folder Setup
1.  Move the contents of the `public` folder from your app to your `public_html` (or subdomain folder).
2.  Edit `public_html/index.php`. Change:
    ```php
    require __DIR__.'/../vendor/autoload.php';
    // to
    require __DIR__.'/../../bulk-email-app/vendor/autoload.php';

    $app = require_once __DIR__.'/../bootstrap/app.php';
    // to
    $app = require_once __DIR__.'/../../bulk-email-app/bootstrap/app.php';
    ```

### 4. Configuration
1.  Copy `.env.example` to `.env` inside `/home/cpuser/bulk-email-app/`.
2.  Edit `.env` (via File Manager or SSH):
    - `APP_URL=https://your-domain.com`
    - `DB_DATABASE=cpuser_bulkemail`
    - `DB_USERNAME=...`
    - `DB_PASSWORD=...`
    - `QUEUE_CONNECTION=database` (If Redis is not available on shared hosting)
    - `AWS_ACCESS_KEY_ID=...`
    - `AWS_SECRET_ACCESS_KEY=...`

### 5. Dependency Installation (SSH Required)
Login via SSH and run:
```bash
cd /home/cpuser/bulk-email-app
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan migrate --force
php artisan storage:link
```
*If you don't have SSH, run `composer install` locally, zip `vendor`, and upload it.*

### 6. Queue Worker (CRITICAL)
For bulk emails to send, the queue must run.
1.  **Ideally**: Use Supervisor. (Available on VPS).
2.  **Shared Layout**: Setup a Cron Job.
    - CPanel > **Cron Jobs**.
    - Command: `cd /home/cpuser/bulk-email-app && php artisan schedule:run >> /dev/null 2>&1`
    - Frequency: Every minute `* * * * *`.
    - **Note**: You might need `php artisan queue:work --stop-when-empty` in a cron if you can't run a daemon. Better approach for shared hosting is to use `database` queue and a cron that processes the queue every minute.

### 7. Recommendations
- Use Redis if possible (many hosting providers offer it).
- Ensure your `QUEUE_CONNECTION` matches your environment (sync for testing, redis/database for production).
