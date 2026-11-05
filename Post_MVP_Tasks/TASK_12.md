TASK 12 — ADD CI QUALITY GATE
Setelah source code fixes 01–10 selesai:
Implement GitHub Actions CI.
Trigger:

push
pull_request
CI minimal harus menjalankan:

checkout
PHP setup sesuai composer requirement
composer install
Node setup
npm ci
frontend production build
test environment setup
Laravel application key
test database configuration
migrations jika diperlukan
php artisan test
Tambahkan caching hanya jika sederhana.
CI tidak boleh membutuhkan production secret untuk normal test.
Acceptance Criteria:
✓ Push menjalankan CI
✓ Pull Request menjalankan CI
✓ Composer install PASS
✓ npm build PASS
✓ Laravel tests PASS
✓ No production credential required
Setelah selesai:
STOP.
