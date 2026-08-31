PHASE 12 — INVENTORY MOVEMENT
Goal
Mencatat setiap perpindahan barang.
Jangan hanya overwrite:

inventory.location_id
Tanpa histori.
Movement
INBOUND
MOVE
OUTBOUND
Data:

inventory_item

from_location
to_location

movement_type
performed_by
timestamp
notes
Example
INV-00021

01 Nov
Receiving
→ A-R01-L01

05 Nov
A-R01-L01
→ B-R02-L03

01 Feb
B-R02-L03
→ Outbound
OUTPUT PHASE 12
✓ Movement history
✓ Location update
✓ Audit trail