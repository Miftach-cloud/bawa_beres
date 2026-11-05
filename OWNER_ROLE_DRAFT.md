# BawaBeres — Draft Role OWNER

## Tujuan Role

OWNER adalah role dengan akses penuh untuk memantau, mengawasi, mengevaluasi, dan melakukan override bila diperlukan.

OWNER bukan role yang wajib menjalankan seluruh proses operasional harian. Pada simulasi normal, OWNER berfungsi sebagai supervisor dan auditor.

## Tanggung Jawab Utama

- Melihat dashboard keseluruhan.
- Memantau order aktif dan selesai.
- Melihat data customer.
- Melihat quotation dan payment.
- Melihat seluruh jadwal operasional.
- Melihat inventory, QR, storage location, dan movement history.
- Melihat audit trail.
- Mengelola konfigurasi sistem.
- Menangani exception/escalation dari ADMIN atau OPERATION.
- Melakukan evaluasi performa bisnis dan operasional.

## Workflow OWNER pada 3 Layanan

### 1. Pindahan Kost
Monitor flow:
Customer Booking → Admin Review → Quotation → Payment → Pickup → Delivery → Completed.

Fokus:
- Harga sesuai.
- Payment sesuai.
- Jadwal terlaksana.
- Tidak ada status yang dilompati sembarangan.
- Complaint/dispute tertangani.

### 2. Penitipan Barang
Monitor flow:
Booking → Payment → Pickup → Receiving → Inventory → QC → Foto → QR → Storage → Movement → Outbound → Redelivery → Released → Completed.

Fokus:
- Barang booking = barang fisik.
- Kondisi barang terdokumentasi.
- QR benar.
- Lokasi rak benar.
- Movement tercatat.
- Barang tidak keluar tanpa proses outbound.

### 3. Delivery / Kurir
Monitor flow:
Booking → Review → Quotation → Payment → Pickup → Delivery → Completed.

Fokus:
- Pickup/destination benar.
- Payment benar.
- Delivery selesai.
- Barang tidak dipaksa masuk warehouse inventory jika hanya transit.

## Exception Handling

OWNER turun tangan jika:
- Payment dispute.
- Customer complaint.
- Barang rusak/hilang.
- Inventory mismatch.
- Storage mismatch.
- Unauthorized status transition.
- Cancellation sensitif.
- Manual override.

## Checklist OWNER

### Daily
- [ ] Cek order baru.
- [ ] Cek order terlambat.
- [ ] Cek schedule hari ini.
- [ ] Cek payment pending.
- [ ] Cek inventory/storage issue.
- [ ] Cek complaint.

### Weekly
- [ ] Review order completed.
- [ ] Review payment discrepancy.
- [ ] Review inventory movement.
- [ ] Review kapasitas storage.
- [ ] Review aktivitas ADMIN.
- [ ] Review aktivitas OPERATION.
- [ ] Review incident/error.
- [ ] Evaluasi SOP.

## Prinsip Role

OWNER = pengawas sistem dan bisnis.

Happy-path normal seharusnya dapat berjalan tanpa OWNER.
