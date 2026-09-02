TASK 08 — REMOVE DUPLICATE STORAGE LOGIC
Audit menemukan dua jalur storage:
Modern flow:
AssignInventoryToLocation
→ storage_location_id
→ movement history
→ occupancy
Legacy flow:
StoreInventoryItem
→ string storage_location
Ini dapat menciptakan inconsistency.
Tugas:

Search seluruh usage:
StoreInventoryItem
storage_location
storage_location_id
AssignInventoryToLocation
Tetapkan source of truth:
storage_location_id
→ StorageLocation entity
Semua assignment storage fisik harus melalui domain Action yang:
menggunakan DB transaction
validate location
update occupancy
record InventoryMovement
Hapus/deprecate legacy Action hanya jika tidak lagi digunakan.
Jangan kehilangan compatibility tanpa test.
storage_location string jika masih dibutuhkan untuk display/history harus ditentukan apakah:
derived,
snapshot,
atau legacy field.
Dokumentasikan keputusan.

Tambahkan tests yang memastikan tidak ada inventory berubah lokasi tanpa movement record.
Acceptance Criteria:
✓ Single storage assignment flow
✓ No bypass movement history
✓ Location FK authoritative
✓ Occupancy synchronized
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
