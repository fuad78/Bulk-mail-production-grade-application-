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

## Deployment Guide: cPanel (Shared Hosting)

Deploying a Laravel app on shared cPanel hosting requires specific steps since you cannot easily change the web root or run long-running processes (Supervisor).

### 1. Database Setup
1.  Log in to cPanel > **MySQL Databases**.
2.  Create a new Database (e.g., `cpuser_bulkmail`).
3.  Create a new User and assign it to the database with **ALL PRIVILEGES**.
4.  Save the database name, user, and password.

### 2. Files Upload
1.  On your local machine, run `composer install --optimize-autoloader --no-dev`.
2.  Zip the entire project folder (including `vendor`).
3.  In cPanel > **File Manager**, create a folder properly outside public access: `/home/cpuser/apps/bulk-mail`.
4.  Upload and extract your zip there.

### 3. Public Folder Setup (The Symlink Method)
*Do not move files. Use a symlink to keep the architecture clean.*

1.  Delete the `public` folder inside `public_html` (or create a subdomain folder).
2.  Create a PHP script named `symlink.php` in `public_html`:
    ```php
    <?php
    $target = '/home/cpuser/apps/bulk-mail/public';
    $link = '/home/cpuser/public_html'; // Or /home/cpuser/public_html/subdomain
    symlink($target, $link);
    echo "Symlink created.";
    ?>
    ```
3.  Run it by visiting `yourdomain.com/symlink.php` in your browser.
4.  Delete `symlink.php`.

### 4. Environment Configuration
1.  Rename `.env.example` to `.env` in `/home/cpuser/apps/bulk-mail/`.
2.  Edit `.env`:
    - `APP_URL=https://your-domain.com`
    - `DB_DATABASE=cpuser_bulkmail`
    - `DB_USERNAME=cpuser_...`
    - `DB_PASSWORD=...`
    - `QUEUE_CONNECTION=database` (**Important:** Redis is rarely available on shared hosting).
    - `AWS_ACCESS_KEY_ID=...`
    - `AWS_SECRET_ACCESS_KEY=...`

### 5. Finalize Installation
1.  **Terminal/SSH** (If available in cPanel):
    ```bash
    cd /home/cpuser/apps/bulk-mail
    php artisan migrate --force
    php artisan storage:link
    php artisan config:cache
    php artisan route:cache
    ```
2.  **No SSH?** Use Routes:
    - Create a temporary route in `web.php` to run migrations:
      ```php
      Route::get('/migrate', function() {
          \Artisan::call('migrate --force');
          return 'Migrated!';
      });
      ```
    - Visit `yourdomain.com/migrate` then remove the code.

### 6. Cron Job for Emails (Required)
Since Shared Hosting kills long-running processes, we use the Scheduler to process the queue.

1.  Go to cPanel > **Cron Jobs**.
2.  Add a new Cron Job (Every Minute `* * * * *`):
    ```bash
    /usr/local/bin/php /home/cpuser/apps/bulk-mail/artisan schedule:run >> /dev/null 2>&1
    ```
    *(Note: Check your host's PHP path. It might be `/usr/bin/php` or `/opt/alt/php82/usr/bin/php`)*

3.  **Kernel Update**:
    Ensure `app/Console/Kernel.php` (or `routes/console.php` in Laravel 11) has the worker command.
    In `routes/console.php`, add:
    ```php
    Schedule::command('queue:work --stop-when-empty')
        ->everyMinute()
        ->withoutOverlapping();
    ```

This ensures that every minute, the server sends a batch of emails until the queue is empty, then stops to respect shared hosting limits.
