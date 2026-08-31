PHASE 7 — PAYMENT MVP
Goal
Mencatat pembayaran tanpa payment gateway.
Payment Method
MVP:

BANK_TRANSFER
QRIS
CASH
Payment Status
PENDING
WAITING_VERIFICATION
PAID
REJECTED
REFUNDED
Payment Proof
Customer/admin dapat upload bukti pembayaran apabila diperlukan.
Admin:

Verify
Reject
IMPORTANT
Jangan menggunakan:

orders.is_paid
sebagai satu-satunya record pembayaran.
Satu order bisa memiliki lebih dari satu payment.
OUTPUT PHASE 7
✓ Payment record
✓ Proof upload
✓ Manual verification
✓ Payment status
✓ Order-payment relation