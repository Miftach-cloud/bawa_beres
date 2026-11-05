TASK 03 — PRIVATE FILE STORAGE & SECURE FILE ACCESS
Audit menemukan file sensitif disimpan pada disk public.
Affected data minimal:

payment proof
inventory photos
booking/estimation photos
File tersebut adalah operational/customer evidence dan tidak boleh menjadi public asset biasa.
Tugas:

Inspect:
Payment upload implementation
InventoryPhoto
UploadInventoryPhoto action
BookingForm photo upload
filesystem config
related views
related tests
Pindahkan sensitive uploads ke PRIVATE storage.
Jangan expose file menggunakan direct public storage URL.
Implement secure authorized access.
Contoh target flow:
authenticated authorized staff
↓
secure route/controller
↓
authorization check
↓
stream/download private file
Gunakan unpredictable stored filename.
Validation wajib mempertahankan:
allowed MIME
allowed extension
max size
Jangan percaya filename dari user.
Pastikan file yang dihapus dari database juga memiliki lifecycle cleanup yang aman jika applicable.
Tambahkan tests:
guest cannot access payment proof
unauthorized role cannot access sensitive file
authorized role can access file
invalid file type rejected
oversized file rejected
direct public URL tidak tersedia
Acceptance Criteria:
✓ Payment proof private
✓ Inventory photo private
✓ Booking photo private
✓ Secure authorized delivery
✓ Tidak ada raw public storage URL untuk evidence
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
