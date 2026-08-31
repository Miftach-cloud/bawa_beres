# Bawa Beres Development Rules & Workflow

## 1. Phased Execution & Evaluation Gatekeeping
- The project follows the phased execution roadmap in `MVP_Phases/` (starting from `Phase_01.md`).
- For every phase, development must adhere to:
  1. Scope definition
  2. Development tasks
  3. Expected outputs
  4. Acceptance criteria
  5. Verification & Testing
  6. Documentation before proceeding
- **STRICT GATE**: The agent is NOT allowed to advance to any subsequent phase before the currently active phase passes all acceptance criteria, testing, and evaluation checkpoints.

## 2. Architecture Constraints
- **Modular Monolith**: All modules (Public Website, Customer Booking, Tracking, Admin Dashboard, Order Management, Inventory, Storage, Payment, Scheduling) MUST reside within a single Laravel application.
- Do NOT build microservices.
- Do NOT separate frontend and backend into decoupled standalone projects.

## 3. Technology Stack Standards
- **Backend**: Laravel (PHP 8.4/8.5), Eloquent ORM.
- **Frontend / UI**: Laravel Blade, Livewire 4, Alpine.js, Tailwind CSS 4, Vite.
- **Database**: MySQL.
- **Storage**: S3-compatible object storage (Local disk storage permitted during local development).
- **Infrastructure**: Linux VPS, Nginx, PHP-FPM, Cloudflare.
