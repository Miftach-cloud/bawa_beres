TASK 13 — FULL REGRESSION TEST
Jangan implement fitur.
Lakukan full project verification.
Run:
composer install / validate as appropriate
php artisan optimize:clear
php artisan test
npm ci
npm run build
PHP formatter/linter existing project
Periksa juga:
php artisan route:list
Pastikan route protected sesuai intended authorization.
Lakukan static review terhadap:

order lifecycle
quotation revision
payment verification
scheduling
inventory generation
inventory receive/check/store
QR
storage relocation
outbound
public booking
tracking
notifications
private attachments
Report exact:
TOTAL TESTS
PASSED
FAILED
SKIPPED
Jika ada failure:
JANGAN abaikan.
Cari root cause dan report.
Tidak boleh declare PASS jika test/build masih gagal.
STOP.
