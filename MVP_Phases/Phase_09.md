PHASE 9 — INVENTORY FOUNDATION
Goal
Membuat sistem untuk barang yang benar-benar berada di tangan perusahaan.
IMPORTANT CONCEPT
Pisahkan:

ORDER ITEM
dan

INVENTORY ITEM
Order Item:

Barang menurut booking.
Inventory Item:

Barang fisik yang benar-benar diterima.
Inventory Flow
EXPECTED
   ↓
RECEIVED
   ↓
CHECKED
   ↓
STORED
   ↓
OUTBOUND
   ↓
RELEASED
Inventory Data
Minimal:

inventory_code
order_id
order_item_id

name
description

condition

status

received_at
released_at
Inventory code:

INV-2026-000001
OUTPUT PHASE 9
✓ Inventory entity
✓ Receiving
✓ Status lifecycle
✓ Inventory-order relation