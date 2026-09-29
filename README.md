# Manajemen Event dan Sertifikat

Aplikasi internal untuk panitia event: pendaftaran peserta dengan QR, check-in di lokasi, generate sertifikat massal lewat antrean background job, dan halaman verifikasi keaslian sertifikat untuk publik.

Latar belakang, alur lengkap, dan kriteria penerimaan tiap fitur ada di [`PRD.md`](PRD.md). Arah desain UI ada di [`DESIGN.md`](DESIGN.md).

## Fitur

- **Pendaftaran publik**: siapa pun bisa mendaftar ke sebuah acara tanpa login. Kuota event ditegakkan (pendaftaran ditolak jujur begitu penuh). Setelah daftar, peserta mendapat kode pendaftaran + QR, dan bisa membuka kembali lewat halaman "cek status pendaftaran" (email + kode).
- **Check-in QR**: staf/panitia (login) memindai QR peserta lewat kamera perangkat (pakai [html5-qrcode](https://github.com/mebjas/html5-qrcode)), dengan input manual sebagai cadangan jika kamera tidak tersedia. Memindai kode yang sudah check-in menampilkan info jujur "sudah check-in sebelumnya", tidak dianggap error maupun sukses palsu.
- **Generate sertifikat massal**: panitia klik "Generate Sertifikat" pada halaman acara. Hanya peserta yang sudah check-in yang dibuatkan sertifikat. Satu job PDF per peserta dikirim ke antrean lewat `Bus::batch()`, benar-benar diproses di background oleh `php artisan queue:work`, bukan sinkron di request. Progres batch (X/Y selesai) tampil live di halaman admin.
- **Verifikasi publik**: `/sertifikat/verifikasi/{token}` bisa dibuka siapa saja tanpa login. Token valid dan sertifikat sudah jadi -> tampil "ASLI dan valid" + tombol unduh PDF. Token tidak dikenal -> tampil jujur "tidak ditemukan", tidak pernah sukses palsu.

## Kebutuhan sistem

- PHP 8.4, Composer 2.x
- Node.js + npm

QR code digenerate sebagai SVG (lewat `simplesoftwareio/simple-qrcode`), jadi tidak butuh ekstensi tambahan seperti Imagick.

## Instalasi

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# database sqlite lokal (untuk deployment sungguhan lihat blok MySQL
# yang dikomentari di .env.example)
touch database/database.sqlite

php artisan migrate --seed
npm run build
```

## Menjalankan aplikasi

Butuh **dua proses berjalan bersamaan**:

```bash
# proses 1: web server
php artisan serve

# proses 2: worker antrean -- WAJIB berjalan, karena generate sertifikat
# diproses secara asynchronous lewat antrean, bukan langsung saat diklik
php artisan queue:work
```

Tanpa `php artisan queue:work` berjalan, klik "Generate Sertifikat" akan membuat baris sertifikat berstatus "Sedang diproses..." yang tidak akan pernah selesai karena job-nya menumpuk di tabel `jobs` dan tidak ada yang mengeksekusi.

Untuk pengembangan sehari-hari, `composer run dev` menjalankan server, queue listener, dan Vite sekaligus dalam satu proses (memakai `php artisan dev`), jadi tidak perlu membuka tiga terminal terpisah.

## Kredensial demo

Seeder (`php artisan migrate --seed`) membuat satu akun panitia demo:

- **Email**: `panitia@kampus.test`
- **Password**: `password`

Ini kredensial untuk lingkungan pengembangan/demo saja, bukan untuk produksi. Seeder juga membuat dua acara contoh (satu sudah selesai dengan beberapa peserta yang sudah check-in, siap untuk dicoba generate sertifikatnya; satu akan datang, siap untuk dicoba pendaftarannya) beserta nama peserta yang sepenuhnya sintetis (bukan orang sungguhan).

## Menjalankan test

```bash
php artisan test
```

Mencakup: kuota pendaftaran ditegakkan, check-in QR idempotent, generate sertifikat hanya untuk peserta check-in, dan halaman verifikasi membedakan token valid/tidak valid secara jujur.

## Struktur alur singkat

1. Peserta daftar di `/acara/{event}/daftar` -> dapat kode + QR.
2. Panitia check-in peserta di `/panitia/checkin` (scan atau ketik manual).
3. Setelah acara selesai, panitia buka `/panitia/acara/{event}` dan klik "Generate Sertifikat".
4. `php artisan queue:work` memproses job satu per satu, PDF tersimpan, progres tampil live di halaman.
5. Siapa pun bisa memverifikasi sertifikat di `/sertifikat/verifikasi/{token}` dan mengunduh PDF-nya dari halaman yang sama.
