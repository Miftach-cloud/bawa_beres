TASK 07 — PROTECT TRANSACTION HISTORY FROM CUSTOMER DELETION
Audit menemukan:
orders.customer_id → cascadeOnDelete()
Ini berisiko menghapus historical transaction ketika customer dihapus.
Tugas:

Inspect seluruh foreign key chain:
Customer
→ Order
→ Quotation
→ Payment
→ Schedule
→ Inventory
→ Movement
Tentukan deletion policy yang aman untuk operational/audit system.
Untuk MVP, historical transaction tidak boleh hilang hanya karena customer dihapus.

Prefer:
soft delete Customer
dan/atau
restrict customer deletion jika memiliki order
Gunakan strategy yang paling konsisten dengan existing architecture.

Jangan destructive migrate existing data.
Tambahkan tests:
customer dengan historical order tidak menyebabkan order hilang
quotation/payment/inventory history tetap tersedia
deletion behavior predictable
soft-deleted customer tidak tampil sebagai active customer bila digunakan
Acceptance Criteria:
✓ Transaction history protected
✓ Audit trail protected
✓ No cascading accidental deletion
✓ Migration safe
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
