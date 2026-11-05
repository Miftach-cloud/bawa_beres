TASK 06 — BUSINESS INFORMATION & SEO CONFIG CLEANUP
Audit menemukan LocalBusiness JSON-LD dan public UI memiliki hardcoded placeholder seperti:

address
phone
latitude
longitude
business hours
Informasi tersebut belum boleh dianggap data bisnis final.
Tugas:

Search seluruh project untuk hardcoded:
phone
WhatsApp
street address
coordinates
operating hours
email
business identity
Buat single source of truth.
Prefer sederhana untuk MVP:
config/business.php
dengan values berasal dari .env jika cocok.
Contoh:
BUSINESS_NAME
BUSINESS_PHONE
BUSINESS_WHATSAPP
BUSINESS_ADDRESS
BUSINESS_CITY
BUSINESS_PROVINCE
BUSINESS_POSTAL_CODE
BUSINESS_LATITUDE
BUSINESS_LONGITUDE
BUSINESS_OPEN_TIME
BUSINESS_CLOSE_TIME
Jangan invent data baru.
Jika data bisnis belum tersedia:
gunakan safe empty/null configuration dan jangan menghasilkan structured data yang mengklaim informasi palsu.
JSON-LD hanya boleh menampilkan field yang valid/configured.
Footer/contact/WhatsApp CTA harus menggunakan source yang sama.
Pertahankan:
canonical
title
description
OG
Twitter metadata
FAQ schema
service SEO
Tambahkan tests supaya placeholder lama tidak muncul.
Acceptance Criteria:
✓ Tidak ada fake business contact
✓ Tidak ada fake address
✓ Tidak ada fake coordinate
✓ One source of truth
✓ Schema menggunakan data real/configured
✓ SEO foundation tetap berjalan
✓ Tests PASS
✓ Build PASS
Setelah selesai:
STOP.
