# BawaBeres — Draft Role ADMIN

## Tujuan Role

ADMIN adalah front-office dan transaction operator.

ADMIN bertanggung jawab mengubah permintaan customer menjadi order operasional yang siap dikerjakan oleh OPERATION.

## Tanggung Jawab Utama

- Menerima booking.
- Review customer/order.
- Memastikan alamat dan barang lengkap.
- Membuat dan mengirim quotation.
- Mencatat acceptance customer.
- Mencatat/verifikasi payment.
- Membuat jadwal pickup/delivery/redelivery.
- Assign tim dan kendaraan.
- Menjadi penghubung Customer ↔ OPERATION.
- Menutup order setelah operasi selesai.

## ADMIN Tidak Bertugas Untuk

- Receiving barang fisik.
- QC barang.
- Menempel QR.
- Menentukan rak secara fisik.
- Memindahkan barang antar rak.
- Menjalankan outbound fisik.

## Workflow Umum ADMIN

Customer Booking
→ Review
→ Quotation
→ Customer Accept
→ Payment
→ Schedule
→ Handoff ke OPERATION

# 1. Pindahan Kost

## A. Review
Cek:
- Customer.
- WhatsApp.
- Pickup.
- Destination.
- Barang.
- Quantity.
- Foto.
- Tanggal.
- Catatan.

Expected: `PENDING_REVIEW`

## B. Quotation
Contoh:
- Jasa Pindahan Rp150.000
- Packing Rp30.000
- Total Rp180.000

Flow:
`PENDING_REVIEW → QUOTED`

## C. Acceptance
Customer setuju via WhatsApp.
ADMIN catat acceptance.

Flow:
`QUOTED → CONFIRMED`

## D. Payment
Catat payment → verifikasi.

Flow:
`CONFIRMED → PAID`

## E. Pickup Schedule
Isi tanggal, jam, team, vehicle.

Flow:
`PAID → SCHEDULED`

## F. Setelah Pickup
OPERATION menyelesaikan pickup.

Expected:
`SCHEDULED → PICKED_UP`

ADMIN memastikan DELIVERY schedule.

## G. Final
Setelah OPERATION delivery:
`IN_TRANSIT → DELIVERED`

ADMIN final check:
`DELIVERED → COMPLETED`

# 2. Penitipan Barang

## A. Review
Pastikan:
- Pickup.
- Barang.
- Quantity.
- Foto.
- Estimasi ukuran.
- Durasi/catatan.
- Contact customer.

## B. Quotation
Contoh:
- Pickup Rp50.000
- Storage kardus Rp100.000
- Monitor Rp40.000
- Koper Rp25.000

Flow:
`PENDING_REVIEW → QUOTED → CONFIRMED`

## C. Payment
Catat/verifikasi.

Flow:
`CONFIRMED → PAID`

## D. Pickup Schedule
Buat schedule PICKUP.

Flow:
`PAID → SCHEDULED`

## E. Handoff ke OPERATION
OPERATION menangani:
- Receiving.
- Inventory.
- QC.
- Photos.
- QR.
- Storage.

Ketika semua barang stored:
`PICKED_UP / PROCESSING → STORED`

## F. Customer Minta Barang Kembali
ADMIN:
- Konfirmasi barang.
- Konfirmasi destination.
- Set `OUTBOUND_REQUESTED`.
- Buat REDELIVERY schedule.
- Assign team/vehicle.

## G. Final
Setelah redelivery:
`DELIVERED → COMPLETED`

# 3. Delivery / Kurir

## A. Review
Cek:
- Pickup.
- Destination.
- Barang.
- Foto.
- Contact sender/recipient.
- Catatan.

## B. Quotation
Flow:
`PENDING_REVIEW → QUOTED → CONFIRMED`

## C. Payment
Flow:
`CONFIRMED → PAID`

## D. Pickup Schedule
Flow:
`PAID → SCHEDULED`

## E. Delivery Schedule
OPERATION melakukan pickup dan delivery.

Flow:
`SCHEDULED → PICKED_UP → IN_TRANSIT → DELIVERED`

ADMIN:
`DELIVERED → COMPLETED`

## Handoff Checklist

Sebelum handoff ke OPERATION:
- [ ] Order valid.
- [ ] Customer confirm.
- [ ] Payment jelas.
- [ ] Schedule dibuat.
- [ ] Alamat lengkap.
- [ ] Team assigned.
- [ ] Vehicle assigned.
- [ ] Item declaration lengkap.
- [ ] Operational notes jelas.

## Prinsip Role

ADMIN bertanggung jawab atas:
Customer + Transaction + Planning.

OPERATION bertanggung jawab atas:
Physical Execution.
