TASK 05 — REMOVE DEVELOPMENT SYSTEM STATUS FROM PUBLIC
Audit menemukan homepage masih merender:
public.system-status
Komponen tersebut dapat menampilkan:

database name
raw database exception message
developer Livewire connectivity test
Ini development-only dan tidak boleh berada di production public website.
Tugas:

Remove SystemStatus dari homepage/public UI.
Remove atau isolate component jika memang masih dibutuhkan untuk local development.
Pastikan raw exception tidak pernah ditampilkan kepada visitor.
Jangan expose database name.
Jangan expose infrastructure information.
Jika health check memang ingin dipertahankan:
buat hanya minimal server-side health mechanism yang TIDAK membocorkan infrastructure detail dan jangan menambah fitur baru jika tidak diperlukan.
Tambahkan/ubah test:

homepage tidak mengandung database name
homepage tidak mengandung "System Foundation Check"
homepage tetap HTTP 200
public page tetap render normal
Acceptance Criteria:
✓ No database information exposed
✓ No raw exception exposed
✓ No development test card
✓ Homepage normal
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
