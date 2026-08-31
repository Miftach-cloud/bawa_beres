PHASE 3 — AUTHENTICATION, ROLE & ADMIN FOUNDATION
Goal
Membuat internal system yang aman.
3.1 Authentication
User internal:

Owner
Admin
Operation
Customer belum wajib login.
3.2 Role
Minimum:

OWNER
ADMIN
OPERATION
3.3 Authorization
Contoh:

OWNER
Full Access
ADMIN
Customers
Orders
Quotation
Payment
Schedule
OPERATION
Schedule
Inventory
Storage
Item Documentation
Gunakan:

Laravel Policies
Gates
jangan hanya menyembunyikan tombol pada UI.
Backend harus tetap melakukan authorization.
3.4 Admin Layout
Buat:

/admin
Navigation awal:

Dashboard
Orders
Customers
Services
Schedule
Inventory
Storage
Payments
Settings
Beberapa menu boleh placeholder dahulu.
3.5 Dashboard MVP
Jangan membuat analytics kompleks.
Cukup:

New Orders
Orders Today
Scheduled Pickups
Active Storage
Pending Payment
OUTPUT PHASE 3
✓ Login
✓ Logout
✓ Roles
✓ Authorization
✓ Admin layout
✓ Dashboard skeleton