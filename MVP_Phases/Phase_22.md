PHASE 22 — AGENT EVALUATION
Setelah semua MVP selesai, jangan langsung menambah fitur.
Minta Agent melakukan audit project.
Evaluation harus mencakup:

Architecture
Fat Controller?
Duplicate logic?
Incorrect relationship?
Business logic leaking to UI?
Database
Indexes
Foreign Keys
Constraints
Naming
Potential N+1
Security
Authorization
Validation
File upload
Mass assignment
Sensitive data exposure
Performance
N+1 queries
Large queries
Missing pagination
Image handling
UX
Broken state
Empty state
Loading state
Error feedback
Responsive
Testing
Missing critical tests
Flaky test
Uncovered business logic
Agent harus menghasilkan:

CRITICAL
HIGH
MEDIUM
LOW
untuk setiap finding.
Critical dan High wajib diselesaikan sebelum MVP dinyatakan siap.
MVP COMPLETION CHECKLIST
MVP dinyatakan selesai jika:

Customer
✓ Bisa membuka website
✓ Bisa memilih layanan
✓ Bisa membuat booking
✓ Bisa upload informasi barang
✓ Mendapat Order Code
✓ Bisa melihat status
Admin
✓ Bisa login
✓ Bisa melihat booking
✓ Bisa mengelola customer
✓ Bisa review order
✓ Bisa membuat quotation
✓ Bisa mencatat payment
✓ Bisa membuat schedule
Operation
✓ Bisa menerima barang
✓ Bisa dokumentasi barang
✓ Bisa membuat inventory
✓ Bisa generate QR
✓ Bisa assign storage
✓ Bisa memindahkan inventory
✓ Bisa melakukan outbound
System
✓ Status history tercatat
✓ Inventory history tercatat
✓ Role authorization bekerja
✓ File upload aman
✓ Critical tests lolos
✓ Responsive
✓ Production build berhasil
FITUR YANG TIDAK MASUK MVP
Agent dilarang mengimplementasikan ini kecuali diminta secara eksplisit:

Mobile App
Driver App
GPS realtime tracking
Automatic route optimization
AI recommendation
Chatbot AI
Microservices
Automatic warehouse optimization
Marketplace
Complex loyalty program
Payment Gateway
Automatic WhatsApp API
Advanced customer dashboard
Complex analytics
Referral system
Subscription system
Fitur tersebut masuk fase setelah MVP.
DEVELOPMENT SEQUENCE
Urutan final development:

01 Project Foundation
        ↓
02 Database Core
        ↓
03 Auth & Permission
        ↓
04 Services & Customers
        ↓
05 Orders
        ↓
06 Quotations
        ↓
07 Payments
        ↓
08 Scheduling
        ↓
09 Inventory
        ↓
10 Documentation
        ↓
11 Storage
        ↓
12 Inventory Movement
        ↓
13 QR
        ↓
14 Public Booking
        ↓
15 Tracking
        ↓
16 Public Website
        ↓
17 SEO Foundation
        ↓
18 Notifications
        ↓
19 Security & Audit
        ↓
20 Testing
        ↓
21 E2E Simulation
        ↓
22 Agent Evaluation
        ↓
             MVP
AGENT DEVELOPMENT RULES
Setiap Agent yang mengerjakan project harus mengikuti aturan berikut.

Rule 1
Jangan mengimplementasikan fitur di luar active phase.

Rule 2
Sebelum coding:

Inspect existing implementation
Understand related models
Understand existing test
Rule 3
Setelah coding:

Run relevant tests
Run formatter/linter
Run build
Review changed files
Rule 4
Jangan menghapus existing behavior tanpa alasan.

Rule 5
Setiap business rule penting harus mempunyai automated test.

Rule 6
Jangan membuat migration destructive tanpa evaluasi.

Rule 7
Gunakan database transaction untuk proses critical seperti:

Order confirmation
Inventory receiving
Inventory movement
Payment verification
Rule 8
Jangan menyimpan sensitive credential dalam repository.

Rule 9
Jangan membuat temporary workaround tanpa dokumentasi.

Rule 10
Jika menemukan requirement yang bertentangan dengan System Design:

Stop implementation bagian tersebut dan dokumentasikan conflict terlebih dahulu.
WORKFLOW MENGGUNAKAN AGENT
Jangan berikan Agent prompt:

"Buat seluruh website sampai selesai."
Gunakan:

SYSTEM DESIGN
      ↓
PHASE
      ↓
TASK
      ↓
IMPLEMENT
      ↓
TEST
      ↓
EVALUATE
      ↓
APPROVE
      ↓
NEXT PHASE
Contoh:

Phase 5
Order Management

Task 5.1
Implement order listing

↓ Agent

Evaluate

↓ Pass

Task 5.2
Implement order detail

↓ Agent

Evaluate

↓ Pass
Dengan cara ini Agent bekerja seperti developer yang mengikuti backlog, bukan seperti generator website.
DEFINITION OF DONE PER TASK
Setiap task hanya dianggap selesai jika:

✓ Requirement implemented
✓ UI functional
✓ Validation implemented
✓ Authorization implemented
✓ Relevant tests added
✓ Tests passing
✓ No obvious regression
✓ Code reviewed
✓ Documentation updated when necessary
FINAL TARGET
Pada akhir MVP, platform bukan hanya:

Website BawaBeres.
Tetapi sebuah operational system yang mampu menghubungkan:

CUSTOMER
     ↓
DIGITAL PLATFORM
     ↓
ADMIN
     ↓
FIELD OPERATION
     ↓
PHYSICAL INVENTORY
     ↓
STORAGE
     ↓
CUSTOMER
Website berfungsi sebagai pintu masuk.
Order System menjadi pusat transaksi.
Inventory System menjadi representasi digital barang fisik.
Storage System menentukan posisi barang.
Admin Platform menghubungkan seluruh proses tersebut.
Itulah fondasi sebelum platform dikembangkan menuju automation dan scale.