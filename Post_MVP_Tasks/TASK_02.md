TASK 02 — FIX USER ROLE DEFAULT SECURITY
Audit menemukan migration/user creation saat ini memiliki risiko default role ADMIN.
Ini berbahaya karena user yang dibuat tanpa role eksplisit dapat menjadi admin.
Tugas:

Inspect migration users + role migration.
Inspect UserFactory.
Inspect DatabaseSeeder.
Inspect semua titik pembuatan User.
Perbaiki sehingga:

Tidak ada user baru yang otomatis memperoleh privilege ADMIN hanya karena role tidak diberikan.
Internal staff creation HARUS menentukan role secara eksplisit.
Existing seeded OWNER/ADMIN/OPERATION tetap dapat dibuat secara eksplisit.
Gunakan solusi schema/application yang paling aman dan sesuai architecture.
Jangan membuat migration destructive terhadap production data.
Jika perubahan schema membutuhkan migration baru:
BUAT migration baru.
Jangan edit historical migration jika berpotensi sudah dijalankan di production-like database.
Tambahkan tests:

creating privileged staff requires explicit intended role
default/fallback tidak menghasilkan privilege admin
OWNER/ADMIN/OPERATION explicit assignment tetap bekerja
Acceptance Criteria:
✓ Tidak ada privilege escalation dari default role
✓ Seeder tetap bekerja
✓ Factory tetap deterministic
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
