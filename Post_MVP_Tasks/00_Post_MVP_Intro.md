BAWABERES — POST-MVP HARDENING & PRODUCTION READINESS
Project: BawaBeres
Target branch/source utama: main
Tujuan tahap ini bukan menambah fitur baru.
Fokus hanya:

memperbaiki hasil audit,
security hardening,
data integrity,
production configuration,
cleanup technical debt,
testing,
dan final production readiness.
DILARANG:

menambah fitur di luar scope task,
redesign besar,
mengubah business flow tanpa alasan,
menghapus behavior yang sudah bekerja,
membuat workaround sementara,
menggabungkan beberapa task tanpa menyelesaikan verification task sebelumnya.
Untuk SETIAP TASK:

Inspect existing implementation.
Inspect related models, actions, migrations, Livewire components, routes, policies/gates, dan existing tests.
Jelaskan singkat root cause sebelum coding.
Implement fix minimal dan konsisten dengan architecture existing.
Tambahkan/update automated test.
Jalankan relevant test.
Jalankan full test suite.
Jalankan formatter/linter.
Jalankan production frontend build.
Review changed files dan potential regression.
Berikan report:
Files changed
Root cause
Fix
Tests added/updated
Test result
Build result
Remaining risk
Jika semua PASS, commit.
STOP dan tunggu task berikutnya.
Jangan lanjut otomatis ke task berikutnya.
