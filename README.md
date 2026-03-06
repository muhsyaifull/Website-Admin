# Laravel SB Admin 2 Project

Sebuah project Laravel lengkap dengan template SB Admin 2 yang sudah terintegrasi dengan sistem authentication dan user management.

## Fitur Utama

✅ **Laravel Framework** (versi 10.x)  
✅ **SB Admin 2** template yang responsive  
✅ **Authentication System** (Login/Logout)  
✅ **Dashboard Admin** dengan statistik  
✅ **User Management** CRUD lengkap  
✅ **Database migrations** dan seeders  
✅ **Sample data** untuk testing

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
DB_DATABASE=website_admin
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

## Login Credentials

### Admin Account

- **Email:** admin@admin.com
- **Password:** password

### User Account

- **Email:** user@user.com
- **Password:** password

## Struktur Project

```
Website-Admin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   ├── DashboardController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       └── Authenticate.php
│   └── Models/
│       └── User.php
├── database/
│   ├── migrations/
│   │   └── create_users_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       └── users/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
├── routes/
│   └── web.php
├── .env
├── composer.json
└── README.md
```

## Halaman & Fitur

### 1. Dashboard

- Statistik total users, active users, admin users
- Recent users list
- Quick actions menu

### 2. User Management

- **List Users:** Tabel dengan pagination dan DataTables
- **Add User:** Form tambah user baru
- **Edit User:** Form edit user existing
- **View User:** Detail lengkap user
- **Delete User:** Hapus user dengan konfirmasi

### 3. Authentication

- **Login Page:** Desain SB Admin 2 yang responsive
- **Session Management:** Remember me functionality
- **Logout:** Dengan modal konfirmasi

## Template & Assets

Project ini menggunakan:

- **SB Admin 2** template dari StartBootstrap
- **Bootstrap 4.6.2** untuk styling
- **Font Awesome 6.4.0** untuk icons
- **jQuery & DataTables** untuk enhanced functionality

Assets di-load dari CDN untuk kemudahan development.

## Next Steps

Untuk pengembangan selanjutnya, Anda bisa menambahkan:

- Role-based permissions
- Profile management
- File upload functionality
- API endpoints
- Email verification
- Password reset functionality
- Advanced reporting

## Support

Jika ada pertanyaan atau butuh bantuan, silakan buat issue atau contact developer.

---

**Happy Coding! 🚀**
