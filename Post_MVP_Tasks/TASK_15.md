TASK 15 — FINAL PHASE 22 AUDIT
Sekarang lakukan ulang Phase 22 secara REAL.
Jangan implement fitur dulu.
Audit project berdasarkan:
ARCHITECTURE

fat components/controllers
duplicate business logic
domain leakage
transaction boundaries
incorrect relationships
DATABASE

foreign keys
indexes
constraints
cascade behavior
nullable fields
naming
integrity
SECURITY

authentication
authorization
ID enumeration
mass assignment
validation
uploads
private files
sensitive information
rate limiting
error leakage
PERFORMANCE

N+1
pagination
heavy queries
loading
storage access
UX

empty states
error states
loading states
responsive behavior
destructive confirmation
role appropriate UI
TESTING

missing critical tests
uncovered business logic
E2E
authorization
file security
state machine
SEO

metadata
canonical
robots
structured data
fake/placeholder business data
Categorize EVERY finding:
CRITICAL
HIGH
MEDIUM
LOW
Untuk setiap finding berikan:

ID
Severity
File
Description
Risk
Recommended fix
JANGAN declare production ready jika masih ada CRITICAL atau HIGH.
Output akhir:
CRITICAL: X
HIGH: X
MEDIUM: X
LOW: X
Security Readiness: XX/100
Architecture Readiness: XX/100
Data Integrity: XX/100
Testing Readiness: XX/100
Production Readiness: XX/100
Decision hanya salah satu:
NOT READY
READY FOR INTERNAL TEST
READY FOR SOFT LAUNCH
STOP.
