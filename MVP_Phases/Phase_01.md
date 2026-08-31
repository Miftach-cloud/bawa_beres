PHASE 1 — PROJECT SETUP & FOUNDATION
Goal
Membuat fondasi project yang bersih, stabil, dan siap dikembangkan.
1.1 Project Initialization
Buat project Laravel baru.
Setup:

Laravel
PHP
Composer
Node.js
NPM
Vite
MySQL
Git
Buat repository Git.
Branch awal:

main
develop
Untuk development:

feature/*
fix/*
1.2 Environment
Siapkan:

.env
.env.example
Environment minimal:

APP_NAME
APP_ENV
APP_URL

DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

FILESYSTEM_DISK

MAIL_*

QUEUE_CONNECTION
Pastikan .env tidak masuk repository.
1.3 Frontend Foundation
Install/configure:

Tailwind CSS
Livewire
Alpine.js
Vite
Buat base layout:

resources/views/layouts/
├── app.blade.php
├── public.blade.php
└── admin.blade.php
1.4 Application Structure
Gunakan struktur yang tetap Laravel-friendly tetapi tidak menaruh seluruh business logic di Controller.
Contoh:

app/

├── Actions/
│   ├── Orders/
│   ├── Inventory/
│   ├── Quotations/
│   └── Payments/
│
├── Enums/
│
├── Http/
│   ├── Controllers/
│   └── Requests/
│
├── Livewire/
│   ├── Admin/
│   └── Public/
│
├── Models/
│
├── Policies/
│
├── Jobs/
│
├── Notifications/
│
└── Services/
1.5 Coding Standards
Agent wajib mengikuti:

PSR Standards
Laravel naming conventions
Form Request Validation
Database transactions untuk critical operation
Policies/Gates untuk authorization
Enums untuk controlled statuses
Service/Action classes untuk business actions
Hindari:

Fat Controller
Business logic besar di Blade
Raw query tanpa alasan
Hardcoded status string
Duplicated logic
OUTPUT PHASE 1
✓ Laravel project berjalan
✓ Database connection berhasil
✓ Livewire berjalan
✓ Tailwind berjalan
✓ Git repository tersedia
✓ Basic layouts tersedia
✓ Application architecture tersedia
ACCEPTANCE TEST
Agent harus membuktikan:

composer test
npm run build
php artisan migrate
berhasil tanpa error.