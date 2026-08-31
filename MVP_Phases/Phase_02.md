PHASE 2 — DATABASE CORE & DOMAIN FOUNDATION
Goal
Mengimplementasikan database hasil System Design.
Jangan langsung membangun UI kompleks.
Fokus pada:

Model + Migration + Relationship + Enum + Factory + Test.
2.1 Core Entities
Implementasikan:

users
customers
services
orders
order_items
addresses
2.2 Customer
Minimal:

id
customer_code
name
phone
email
notes
created_at
updated_at
Customer code contoh:

CUS-2026-000001
Customer tidak wajib mempunyai account.
2.3 Services
Minimal:

id
name
slug
description
pricing_type
is_active
Pricing type:

FIXED
PACKAGE
QUOTATION
Jangan hardcode daftar layanan di source code.
Service harus bisa dikelola melalui database.
2.4 Orders
Minimal:

id
order_code
customer_id
service_id

status

customer_notes
admin_notes

created_at
updated_at
Contoh:

ORD-2026-000001
Gunakan Enum:

OrderStatus
2.5 Order Items
Contoh field:

id
order_id
name
description
quantity
estimated_size
notes
Order Item berarti:

Barang yang dideklarasikan customer ketika membuat order.
Belum berarti inventory fisik.
2.6 Address
Jangan menyimpan pickup address langsung di orders.
Buat entity:

addresses
Fields:

id
order_id

type

address
city
district

latitude
longitude

notes
Type:

PICKUP
DESTINATION
2.7 Status History
Buat:

order_status_histories
Minimal:

id
order_id
from_status
to_status
changed_by
notes
created_at
Setiap perubahan status penting harus dicatat.
OUTPUT PHASE 2
✓ Migration
✓ Model
✓ Relationship
✓ Enum
✓ Factory
✓ Seeder
✓ Database test
ACCEPTANCE TEST
Agent harus membuat feature/unit test minimal:

Customer can have orders
Order belongs to service
Order can have multiple items
Order can have addresses
Status history is created
Order code is unique
Customer code is unique