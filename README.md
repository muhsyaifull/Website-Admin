# Laravel SB Admin 2 Project

Sebuah project Laravel lengkap dengan template SB Admin 2 yang sudah terintegrasi dengan sistem authentication dan user management.

## Instalasi & Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Environment

```bash
copy .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database

Edit file `.env` dan sesuaikan pengaturan database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="databasename"
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Storage Links

```bash
php artisan storage:link
```

### 5. Jalankan Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

### 6. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`
