# Wedding Invitation App

Bilingual (Indonesian + English) wedding invitation web application built with Laravel 11 + MySQL.

## Features

- **Personalized invitations** — unique URL per guest (`/invite/{token}`)
- **RSVP system** — guests confirm attendance directly on their invitation page
- **Admin panel** — manage wedding details, guest list, and RSVP responses
- **Dark gold aesthetic** matching `zif.my.id`
- **Bilingual** — Indonesian primary, English secondary throughout
- **QR codes** — generated client-side per guest for printing/sharing
- **CSV export** — download full guest list with RSVP status
- **PDF / print** — clean print CSS on invitation page

## Tech Stack

- PHP 8.2+ / Laravel 11
- MySQL 8
- Laravel Breeze (auth)
- Blade templates + Vite
- Google Fonts (Playfair Display, Inter, Amiri)
- QRCode.js (CDN, client-side)

---

## Setup

```bash
git clone <repo-url>
cd wedding-invitation

composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wedding_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
npm install && npm run build
php artisan serve
```

## Admin Credentials

| Field    | Value                  |
|----------|------------------------|
| Email    | admin@wedding.local    |
| Password | wedding2025            |

> Change the password after first login.

## Routes

| URL | Description |
|-----|-------------|
| `/login` | Admin login |
| `/admin/dashboard` | Overview stats |
| `/admin/wedding` | Edit wedding details |
| `/admin/guests` | Guest list + bulk import |
| `/admin/guests/export` | Download CSV |
| `/admin/rsvp` | RSVP responses |
| `/invite/{token}` | Guest invitation page |

## VPS Deployment (Nginx + PHP-FPM)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/wedding/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
# On VPS
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Database Schema

```
weddings   — wedding details (one record)
guests     — guest list with unique tokens
rsvps      — RSVP responses (one per guest)
users      — admin accounts
```
