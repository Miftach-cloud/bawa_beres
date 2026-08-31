PHASE 5 — ORDER MANAGEMENT
Goal
Membuat pusat aplikasi.
Ini merupakan salah satu fase paling penting.
5.1 Order List
Admin:

Search
Filter
Pagination
Status Filter
Service Filter
Date Filter
5.2 Order Detail
Halaman order harus menampilkan:

Order Code

Customer
Service
Items
Pickup Address
Destination
Notes
Status
History
Quotation
Payment
Schedule
Inventory
Beberapa section belum harus aktif jika modul belum dibuat.
5.3 Order Actions
Agent harus membuat action terpisah seperti:

CreateOrder
UpdateOrder
ChangeOrderStatus
CancelOrder
5.4 State Transition
System tidak boleh membiarkan status lompat bebas.
Contoh:

NEW
↓
REVIEW
↓
QUOTED
↓
CONFIRMED
↓
SCHEDULED
↓
PROCESSING
Invalid:

NEW → COMPLETED
Backend harus menolak transition yang tidak valid.
OUTPUT PHASE 5
✓ Order CRUD
✓ Order detail
✓ Order filtering
✓ Status transitions
✓ Status history
✓ Authorization