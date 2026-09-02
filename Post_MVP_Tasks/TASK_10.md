TASK 10 — REMOVE SERVICE-NAME BUSINESS LOGIC
Audit menemukan logic seperti:
service name contains "storage"
atau
service name contains "titip"
digunakan untuk menentukan requirement destination.
Business rule tidak boleh bergantung pada copywriting nama service.
Tugas:

Inspect Service model/schema/seeder.
Inspect BookingForm.
Search logic berdasarkan service->name.
Tambahkan explicit business capability fields.
Gunakan design minimal dan scalable.
Contoh:
requires_pickup
requires_destination
requires_storage
atau service_type enum jika lebih cocok dengan current design.
Jangan overengineer.

Booking validation harus menggunakan configured service behavior.
Existing services di seeder harus diberi configuration yang sesuai.
Tambahkan test:
moving requires destination
delivery requires destination
storage tidak memerlukan destination jika flow memang demikian
rename service tidak mengubah business behavior
Acceptance Criteria:
✓ Business rules independent dari service name
✓ Service rename safe
✓ Booking validation deterministic
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
