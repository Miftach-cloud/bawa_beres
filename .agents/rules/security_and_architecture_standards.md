# BawaBeres Security & Architecture Guidelines

## Authorization & Internal Access
- Internal admin routes must always use `['auth', 'can:access-admin']`.
- Livewire admin components must enforce `Gate::authorize()` inside `mount()` for defense-in-depth.
- Default user creation (database migration and factories) must never assign internal roles by default; roles must be explicitly declared (`UserRole::OWNER`, `ADMIN`, `OPERATION`).

## Sensitive Media & Storage
- Never store customer-uploaded files (payment proofs, estimation photos, condition photos) in public storage.
- Always use the private `'local'` disk.
- Deliver sensitive media exclusively via `SecureFileController` with strict Gate authorization and no-cache headers.

## Public Scanners & QR Security
- Public scan routes must only accept opaque random tokens (`qr_code`).
- Never allow public enumeration using sequential codes (`inventory_code`).
- Conceal warehouse internal locations (rack codes) and customer personal data from unauthenticated public visitors.

## Domain Decoupling & Capabilities
- Never inspect `service->name` strings (`str_contains`) for validation or business workflows.
- Always use configured capability flags on the `Service` model (`requires_destination`, `requires_pickup`, `requires_storage`).

## Data Integrity & Transaction Preservation
- Customer records must use `SoftDeletes`, and model-level delete guards must forbid permanent deletion (`forceDelete`) when transaction orders exist.
- Downstream relationships on orders to customer must use `->withTrashed()`.
- Storage rack assignment must strictly use the authoritative `AssignInventoryToLocation` action to guarantee movement logs and rack capacity synchronization.
