TASK 01 — SECURE ADMIN ACCESS
Audit menemukan bahwa /admin secara global hanya dilindungi authentication dan belum menggunakan gate access-admin.
Tugas:

Inspect:
routes/web.php
AppServiceProvider / Gate definitions
User model
UserRole enum
admin login component/controller
authorization tests
Pastikan seluruh route internal /admin/* membutuhkan:
authenticated user
role internal yang valid
Terapkan global admin access protection menggunakan existing authorization architecture.
Target konsep:
auth
+
can:access-admin
atau implementasi Laravel equivalent yang paling sesuai existing project.

Login admin harus menolak user yang tidak memiliki role internal yang valid.
Allowed internal roles MVP:
OWNER
ADMIN
OPERATION
Jangan hanya menyembunyikan menu.
Backend route HARUS benar-benar protected.
Tambahkan tests:
Guest → admin dashboard = redirect login
OWNER → admin dashboard = allowed
ADMIN → admin dashboard = allowed
OPERATION → admin dashboard = allowed
User dengan role tidak valid/non-internal → forbidden / tidak dapat masuk admin
Pastikan existing role matrix per module tetap bekerja.
Acceptance Criteria:
✓ /admin tidak cukup hanya dengan auth
✓ Global admin authorization aktif
✓ Module authorization tetap berjalan
✓ Unauthorized user tidak dapat mengakses admin melalui URL langsung
✓ Tests PASS
✓ Production build PASS
Setelah selesai:
STOP.
