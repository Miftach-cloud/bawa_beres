PHASE 6 — QUOTATION SYSTEM
Goal
Admin dapat memberikan harga kepada customer.
6.1 Quotation
Buat:

quotations
quotation_items
Quotation status:

DRAFT
SENT
ACCEPTED
REJECTED
EXPIRED
6.2 Quotation Items
Contoh:

Pickup              Rp 80.000
Packing              Rp 40.000
Handling             Rp 20.000
--------------------------------
Subtotal             Rp140.000
Discount             Rp 10.000
--------------------------------
Total                Rp130.000
6.3 Revision
Satu order dapat memiliki beberapa quotation.
Contoh:

Quotation #1
Rejected

Quotation #2
Accepted
Jangan overwrite quotation lama.
OUTPUT PHASE 6
✓ Create quotation
✓ Quotation revision
✓ Quotation history
✓ Accept/reject state
✓ Final quotation detection