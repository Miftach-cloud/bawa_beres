TASK 04 — HARDEN INVENTORY QR ACCESS
Audit menemukan QR scan masih menerima:
qr_code
OR
inventory_code
Inventory code bersifat predictable sehingga dapat dienumerasi.
Public visitor juga tidak boleh mengetahui detail internal seperti lokasi rak.
Tugas:

Inspect:
InventoryScan Livewire
QR generator
InventoryItem
QR routes
QR label
tests
Public QR route hanya boleh resolve inventory menggunakan secure opaque QR token.
JANGAN menerima sequential inventory_code sebagai public identifier.

Inventory code tetap boleh digunakan untuk pencarian INTERNAL setelah authentication jika dibutuhkan.
Untuk unauthenticated visitor, tampilkan hanya data minimal yang aman.
Contoh public information:

Item terdaftar di BawaBeres
inventory reference bila dianggap aman
item status umum
verification/authenticity status
JANGAN tampilkan ke public:

exact storage rack
customer identity
customer phone
internal movement
internal notes
staff identity
sensitive photos
internal order details
Staff yang sudah authenticated dan authorized tetap bisa melihat operational detail.
Pertahankan rate limiting QR endpoint.
Tambahkan tests untuk:
valid opaque token
invalid opaque token
inventory_code tidak dapat digunakan sebagai public lookup
guest tidak melihat storage rack
guest tidak melihat customer data
staff authorized melihat operational detail
rate limiting tetap aktif
Acceptance Criteria:
✓ QR opaque identifier only
✓ Tidak enumerable melalui inventory code
✓ Public/internal views terpisah
✓ Storage location tidak bocor ke public
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
