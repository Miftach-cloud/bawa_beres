TASK 09 — PERSIST BOOKING ATTACHMENTS
Audit menemukan booking photo saat ini disimpan ke filesystem tetapi tidak mempunyai proper persistent metadata relation.
Tugas:

Inspect BookingForm.
Inspect existing inventory photo architecture.
Jangan menyalahgunakan InventoryPhoto jika barang belum menjadi InventoryItem.
Buat architecture minimal untuk order/booking attachment bila belum ada.
Contoh entity:
OrderAttachment
fields minimal:

id
order_id
type
file_path
original_name jika memang diperlukan
mime_type
file_size
timestamps
Store file ke PRIVATE storage mengikuti Task 03.
Create database record atomically/reliably.
Jika DB operation gagal, jangan meninggalkan orphan file jika memungkinkan.
Admin order detail harus dapat mengakses attachment sesuai authorization existing.
Tambahkan tests:
valid booking image creates metadata
order relationship works
unauthorized cannot access
invalid upload rejected
file + DB consistency
Acceptance Criteria:
✓ Booking photo tidak orphan
✓ Proper DB metadata
✓ Private storage
✓ Authorized access
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
