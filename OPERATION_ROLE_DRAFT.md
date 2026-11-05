# BawaBeres — Draft Role OPERATION

## Tujuan Role

OPERATION adalah role tim lapangan dan gudang.

Tanggung jawab OPERATION dimulai setelah order sudah siap secara transaksi dan dijadwalkan oleh ADMIN.

## Tanggung Jawab Utama

- Melihat jadwal operasional.
- Menjalankan pickup.
- Menjalankan delivery/redelivery.
- Receiving barang di basecamp.
- Membuat/menangani inventory fisik.
- QC kondisi barang.
- Upload foto dokumentasi.
- Generate/print/scan QR.
- Assign storage location.
- Memindahkan inventory.
- Menjalankan outbound.
- Release barang.

## OPERATION Tidak Bertugas Untuk

- Membuat quotation.
- Mengubah harga.
- Verifikasi payment.
- Mengelola customer master.
- Mengubah service catalog.
- Membatalkan transaksi administratif.

# 1. Pindahan Kost

## A. Pickup
Buka schedule PICKUP.

Cek:
- Order Code.
- Customer.
- Phone.
- Pickup.
- Destination.
- Team.
- Vehicle.
- Barang.
- Notes.

Klik `Mulai Pengerjaan`.

Di lokasi:
1. Hubungi customer.
2. Cocokkan barang.
3. Angkut.
4. Pastikan tidak ada yang tertinggal.

Klik `Selesai`.

Expected:
`SCHEDULED → PICKED_UP`

## B. Delivery
Buka schedule DELIVERY.
Klik `Mulai Pengerjaan`.

Expected:
`PICKED_UP → IN_TRANSIT`

Sampai tujuan:
- Serahkan barang.
- Cocokkan quantity.

Klik `Selesai`.

Expected:
`IN_TRANSIT → DELIVERED`

## C. Inventory?
Pindahan biasa tidak perlu Inventory/QR/Storage kecuali barang masuk gudang.

# 2. Penitipan Barang

## A. Pickup
Buka schedule PICKUP.
Klik `Mulai Pengerjaan`.

Di lokasi:
- Cocokkan barang.
- Catat mismatch.
- Perhatikan kondisi awal.
- Ambil barang.

Klik `Selesai`.

Expected:
`SCHEDULED → PICKED_UP`

## B. Generate Inventory
Saat barang tiba di basecamp:
Item & QR Label → cari Order → Generate Inventory.

Contoh:
5 Kardus + 1 Monitor
menjadi:
- Kardus #1
- Kardus #2
- Kardus #3
- Kardus #4
- Kardus #5
- Monitor

Expected: `EXPECTED`

## C. Receiving
Setiap item → `Terima Barang`.

Expected:
`EXPECTED → RECEIVED`

## D. QC
Periksa kondisi:
- Lecet.
- Retak.
- Sobek.
- Penyok.
- Pecah.
- Elektronik.
- Packaging.

Expected:
`RECEIVED → CHECKED`

## E. Dokumentasi
Upload:
- RECEIVING.
- CONDITION.
Tambahan:
- DAMAGE.
- STORAGE.
- OUTBOUND.

## F. QR
- Generate.
- Print.
- Tempel.
- Scan ulang.
- Pastikan QR sesuai item.

## G. Storage
Assign location, contoh:
`MLG01-A-R01-L01`

Expected:
`CHECKED → STORED`

Jika semua item stored, order menjadi `STORED`.

## H. Relocate
Jika pindah rak, selalu gunakan action relocate dan pastikan movement history tercatat.

## I. Outbound
Saat ADMIN membuat outbound request:
- Cari inventory.
- Scan QR.
- Ambil dari rak.
- Outbound.

Expected:
`STORED → OUTBOUND`

## J. Redelivery
Buka REDELIVERY schedule.
Klik `Mulai Pengerjaan`.

Antar barang ke customer.

Setelah diterima:
- Schedule selesai.
- Inventory di-release.

Expected:
`OUTBOUND → RELEASED`
dan order menuju `DELIVERED`.

# 3. Delivery / Kurir

## A. Pickup
Buka PICKUP schedule.
Klik `Mulai Pengerjaan`.

- Verifikasi sender.
- Verifikasi paket.
- Cocokkan destination.
- Ambil paket.

Klik `Selesai`.

Expected:
`PICKED_UP`

## B. Delivery
Buka DELIVERY schedule.
Klik `Mulai Pengerjaan`.

Expected:
`IN_TRANSIT`

Sampai recipient:
- Verifikasi penerima.
- Serahkan barang.
- Selesai.

Expected:
`DELIVERED`

## C. Inventory?
Delivery normal tidak perlu warehouse inventory/QR/storage.

Flow:
Sender → BawaBeres → Recipient.

# Operational Handoff Checklist

Sebelum misi:
- [ ] Order Code.
- [ ] Customer Name.
- [ ] Phone.
- [ ] Pickup.
- [ ] Destination jika ada.
- [ ] Schedule.
- [ ] Team.
- [ ] Vehicle.
- [ ] Item Declaration.
- [ ] Notes.

Jika informasi kurang:
jangan menebak. Hubungi ADMIN.

# Incident Handling

Jika ada:
- Barang tidak sesuai booking.
- Quantity berbeda.
- Barang rusak.
- Customer tidak ada.
- Alamat salah.
- Kendaraan tidak cukup.
- Rak penuh.
- QR salah.
- Barang hilang.

Lakukan:
1. Stop affected process.
2. Dokumentasikan.
3. Foto jika relevan.
4. Catat notes.
5. Hubungi ADMIN.
6. Eskalasi OWNER jika serius.

## Prinsip Role

OPERATION bertanggung jawab terhadap barang fisik dan eksekusi nyata.

Tindakan fisik dan pencatatan sistem harus berjalan bersama.
