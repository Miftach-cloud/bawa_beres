TASK 14 — E2E SOFTWARE SIMULATION
Lakukan automated/full software E2E simulation:
Customer
↓
Public Booking
↓
Order Code
↓
Admin Review
↓
Quotation
↓
Quotation Accepted
↓
Payment
↓
Payment Verified
↓
Schedule
↓
Pickup
↓
Inventory Generated
↓
Inventory Received
↓
QC
↓
Photo
↓
QR
↓
Storage Assignment
↓
Relocation
↓
Outbound
↓
Delivery
↓
Completed
Validasi sepanjang flow:

OrderStatusHistory
payment state
inventory state
inventory movement
storage occupancy
authorization
tracking output
notifications
Jangan mock business logic utama jika dapat menggunakan application actions sebenarnya.
Jika E2E test existing sudah mencakup flow ini:
review dan perluas hanya gap yang penting.
Acceptance Criteria:
✓ Full lifecycle completes
✓ No illegal status jump
✓ Audit history intact
✓ Inventory movement intact
✓ Payment intact
✓ Storage state intact
✓ Tests PASS
STOP.
