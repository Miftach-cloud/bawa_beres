MVP PLATFORM DEVELOPMENT PLAN
Moving, Storage & Delivery Platform — Kota Malang
Development Approach
Platform dikembangkan secara bertahap menggunakan AI Agent sebagai development assistant/agent utama, dengan setiap fase memiliki:

Scope yang jelas.
Task development.
Expected output.
Acceptance criteria.
Testing.
Evaluation checkpoint.
Dokumentasi sebelum lanjut fase berikutnya.
Prinsip utama:

Agent tidak diperbolehkan melanjutkan ke fase berikutnya sebelum fase aktif lolos evaluasi.
0. MVP OBJECTIVE
Tujuan MVP bukan membuat seluruh visi platform secara langsung.
MVP dianggap berhasil apabila satu transaksi dapat berjalan secara end-to-end:

Customer
   ↓
Website
   ↓
Booking
   ↓
Admin Review
   ↓
Quotation
   ↓
Customer Confirmation
   ↓
Payment Status
   ↓
Scheduling
   ↓
Operation
   ↓
Inventory / Delivery
   ↓
Completed
Untuk layanan storage:

Order
  ↓
Pickup
  ↓
Receiving
  ↓
Item Verification
  ↓
Photo Documentation
  ↓
Create Inventory
  ↓
QR Label
  ↓
Assign Storage Location
  ↓
Stored
  ↓
Outbound Request
  ↓
Redelivery
  ↓
Released
  ↓
Completed
MVP TECHNOLOGY STACK
Backend
Laravel 13
PHP 8.4/8.5
Eloquent ORM
Frontend
Blade
Livewire 4
Alpine.js
Tailwind CSS 4
Vite
Database
MySQL
Infrastructure
Git
GitHub
Linux VPS
Nginx
PHP-FPM
Cloudflare
Storage
S3-compatible object storage untuk:

foto barang;
dokumentasi kondisi;
bukti pembayaran;
file order.
Local storage boleh digunakan selama development.
DEVELOPMENT PRINCIPLE
Gunakan pendekatan:

Modular Monolith
Satu Laravel Application menangani:

Public Website
Customer Booking
Tracking
Admin Dashboard
Order Management
Inventory
Storage
Payment
Scheduling
Tidak menggunakan microservices pada MVP.
Tidak membuat frontend dan backend sebagai project terpisah.