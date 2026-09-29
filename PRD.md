# PRD: Manajemen Event dan Sertifikat

## 1. Latar Belakang

Panitia event (seminar, workshop, pelatihan) selama ini mengelola pendaftaran peserta lewat Google Form atau spreadsheet manual, melakukan absensi dengan tanda tangan di atas kertas, dan membuat sertifikat satu per satu di Word/Canva lalu mengirimkannya manual lewat email. Proses ini lambat (bisa berhari-hari untuk ratusan peserta), rawan salah nama, dan sertifikatnya mudah dipalsukan karena tidak ada cara untuk memverifikasi keasliannya.

Aplikasi ini menggantikan seluruh rangkaian proses tersebut dengan satu sistem: pendaftaran online dengan QR code pribadi, check-in di lokasi dengan pemindaian QR, generate sertifikat massal secara otomatis di background (tanpa membekukan aplikasi saat memproses ratusan sertifikat), dan halaman verifikasi publik agar siapa pun (perusahaan, kampus, atau peserta lain) bisa mengecek keaslian sertifikat hanya dengan membuka sebuah link/QR.

## 2. Tujuan

1. Memangkas waktu pembuatan sertifikat dari hitungan hari menjadi hitungan menit untuk ratusan peserta, dengan proses generate yang berjalan di antrean background job (tidak menghambat panitia yang sedang mengoperasikan aplikasi).
2. Memastikan hanya peserta yang benar-benar hadir (check-in) yang mendapat sertifikat.
3. Menyediakan mekanisme anti-pemalsuan: setiap sertifikat punya token verifikasi unik yang bisa dicek publik.
4. Mengurangi kesalahan input manual (nama peserta di sertifikat diambil langsung dari data pendaftaran, bukan diketik ulang).

## 3. Aktor

| Aktor | Deskripsi | Butuh Login? |
|---|---|---|
| **Admin/Panitia** | Membuat event, memantau pendaftaran, melakukan/mendelegasikan check-in, memicu generate sertifikat massal, memantau progres job. | Ya |
| **Peserta** | Mendaftar ke event, menerima QR pendaftaran, check-in di lokasi, mengunduh sertifikat setelah event selesai. | Tidak (pendaftaran & cek status publik), tapi identitas dicocokkan lewat email + kode pendaftaran |
| **Publik** | Siapa pun yang menerima/menemukan sertifikat dan ingin memverifikasi keasliannya (misal HRD perusahaan). | Tidak |

## 4. Lingkup Fitur

### Termasuk (in-scope)
- CRUD event oleh admin (nama, deskripsi, lokasi, waktu mulai/selesai, kuota opsional).
- Form pendaftaran publik per event, dengan validasi kuota.
- QR pendaftaran (berisi `registration_code`) ditampilkan di halaman konfirmasi dan bisa dilihat ulang lewat pencarian status.
- Halaman scanner QR check-in (staff, login) dengan fallback input manual kode.
- Generate sertifikat massal via background job queue (`Bus::batch`), hanya untuk peserta yang sudah check-in.
- Progres batch generate ditampilkan real-time (polling) di halaman admin: jumlah selesai/total, kegagalan jika ada.
- PDF sertifikat berisi nama peserta, nama & tanggal event, nomor sertifikat, dan QR verifikasi.
- Halaman publik verifikasi sertifikat berdasarkan token, tanpa login.
- Unduh PDF sertifikat dari halaman verifikasi (jika sudah generate).

### Tidak termasuk (out-of-scope, untuk versi ini)
- Pembayaran/tiket berbayar.
- Notifikasi email/WhatsApp otomatis (peserta cek status manual).
- Multi-tenant / multi-organisasi (satu instalasi = satu penyelenggara).
- Custom desain sertifikat per event (satu template, cukup untuk kebutuhan saat ini).

## 5. Entitas Data Utama

### `events`
- `id`, `name`, `description`, `location`, `starts_at`, `ends_at`, `quota` (nullable, null = tanpa batas).

### `registrations`
- `id`, `event_id`, `name`, `email`, `phone`, `registration_code` (unik, 8 karakter acak, contoh `A1B2C3D4`), `qr_payload` (string yang di-encode ke QR), `status` (`registered` / `checked_in`), `checked_in_at` (nullable).
- **Keputusan desain**: `qr_payload` = `registration_code` itu sendiri. Kode 8 karakter acak (huruf besar + angka, ruang sampel ~2.8 triliun kombinasi) sudah cukup sulit ditebak untuk skala event ini, dan menyimpannya sebagai kolom terpisah (bukan hardcode `registration_code` di semua tempat) memberi ruang untuk mengganti skema payload (misal jadi token bertanda tangan) di masa depan tanpa migrasi ulang logika QR.
- Kuota ditegakkan di level aplikasi: pendaftaran ditolak dengan pesan jujur ("Kuota event ini sudah penuh") begitu jumlah `registrations` pada event mencapai `quota`.

### `certificates`
- `id`, `registration_id` (unik, satu sertifikat per pendaftaran), `certificate_number` (unik, format `CERT-{tahun}-{6 digit}`, contoh `CERT-2026-000123`), `verification_token` (unik, string acak 32 karakter, dipakai di URL publik, sengaja berbeda dari `certificate_number` supaya nomor boleh dicetak/dipamerkan sedangkan token tetap jadi kunci pencarian yang tidak mudah ditebak), `file_path` (nullable sampai berhasil digenerate), `status` (`pending` / `generated` / `failed`), `generated_at` (nullable).

### Tabel pendukung
- `job_batches` (bawaan Laravel `Bus::batch`, dibuat via `queue:batches-table`) untuk melacak progres batch generate sertifikat.
- `jobs` (bawaan `QUEUE_CONNECTION=database`) sebagai antrean job background.

## 6. Alur End-to-End

1. **Peserta mendaftar**: membuka halaman publik event, mengisi nama/email/telepon. Sistem membuat `registrations` baru (`status=registered`), generate `registration_code` unik, dan menampilkan QR + kode di halaman konfirmasi. Jika kuota event penuh, pendaftaran ditolak dengan pesan jelas sebelum data disimpan.
2. **Check-in di lokasi**: pada hari event, staff membuka halaman scanner (login), memindai QR peserta lewat kamera perangkat (atau mengetik manual kode jika kamera tidak tersedia). Sistem mencari `registration_code`, jika ditemukan dan belum check-in: set `status=checked_in`, `checked_in_at=now()`. Jika kode sudah pernah check-in: tampilkan info jujur "sudah check-in sebelumnya pukul ..." (bukan sukses palsu, bukan error). Jika kode tidak ditemukan: tampilkan pesan kode tidak valid.
3. **Event selesai**: panitia membuka halaman detail event, melihat daftar peserta yang check-in, klik "Generate Sertifikat".
4. **Generate massal (background)**: sistem membuat satu baris `certificates` (`status=pending`) untuk setiap `registrations` yang `checked_in` dan belum punya sertifikat, lalu mendispatch satu job `GenerateCertificatePdf` per sertifikat di dalam satu `Bus::batch()`. Tiap job merender PDF (nama, event, tanggal, nomor sertifikat, QR verifikasi), menyimpan file ke storage, dan mengupdate baris jadi `status=generated`. Halaman admin polling status batch (`X/Y selesai`) sampai selesai.
5. **Verifikasi/unduh**: peserta (dari link kode QR sertifikatnya) atau siapa pun membuka `/sertifikat/verifikasi/{verification_token}`. Jika token cocok dengan sertifikat yang sudah `generated`: tampilkan detail (nama, event, nomor sertifikat, tanggal generate) dengan status "ASLI/valid" dan tombol unduh PDF. Jika token tidak ditemukan: tampilkan pesan jujur bahwa sertifikat tidak ditemukan, tanpa memberi kesan valid.

## 7. Kriteria Penerimaan per Fitur Inti

### Fitur 1: Pendaftaran
- [ ] Form publik per event bisa diisi tanpa login dan menyimpan data ke `registrations`.
- [ ] `registration_code` yang dihasilkan unik (tidak pernah duplikat lintas semua pendaftaran).
- [ ] Jika `quota` event terisi penuh, pendaftaran baru ditolak dengan pesan yang menyebut kuota penuh, dan tidak ada baris baru tersimpan.
- [ ] Halaman konfirmasi menampilkan QR yang benar-benar berisi `registration_code` peserta tersebut (bisa dipindai ulang dan menghasilkan kode yang sama).
- [ ] Peserta bisa membuka kembali QR/status pendaftarannya lewat pencarian email + kode pendaftaran, tanpa login.

### Fitur 2: Check-in QR
- [ ] Hanya user yang login (staff/admin) yang bisa mengakses halaman scanner.
- [ ] Memindai kode valid yang belum check-in: status berubah jadi `checked_in`, `checked_in_at` terisi.
- [ ] Memindai kode yang sudah check-in: tidak mengubah `checked_in_at` semula, dan menampilkan pesan bahwa peserta ini sudah check-in beserta waktunya (idempotent).
- [ ] Tersedia input manual kode sebagai fallback saat kamera tidak bisa dipakai, dengan hasil yang identik dengan hasil scan kamera.
- [ ] Kode yang tidak dikenal menampilkan pesan "kode tidak ditemukan", bukan error mentah/500.

### Fitur 3: Generate sertifikat massal
- [ ] Tombol "Generate Sertifikat" hanya memproses `registrations` dengan `status=checked_in`; peserta yang tidak check-in tidak mendapat baris `certificates`.
- [ ] Proses generate mendispatch job ke queue (`Bus::batch`) dan dieksekusi oleh `php artisan queue:work`, bukan diproses sinkron di request HTTP (dibuktikan: halaman admin tetap responsif sebelum worker jalan, dan baris `certificates` baru berubah dari `pending` ke `generated` setelah worker memproses).
- [ ] Halaman admin menampilkan progres batch (jumlah selesai dari total) yang bertambah secara live selama worker berjalan (via polling).
- [ ] Setiap PDF yang berhasil dibuat memuat nama peserta, nama event, tanggal event, nomor sertifikat unik, dan QR yang mengarah ke URL verifikasi sertifikat tersebut.
- [ ] Setelah batch selesai, admin bisa mengunduh tiap PDF yang berhasil dibuat langsung dari daftar sertifikat event.

### Fitur 4: Verifikasi publik
- [ ] Token yang valid dan sertifikatnya sudah `generated` menampilkan nama peserta, event, nomor sertifikat, tanggal generate, dan status tervalidasi.
- [ ] Token yang tidak ada di database menampilkan pesan "tidak ditemukan/tidak valid" yang jujur, tanpa data palsu apa pun.
- [ ] Halaman ini bisa diakses tanpa login sama sekali.
- [ ] Tombol "Unduh PDF" hanya muncul dan berfungsi ketika sertifikat sudah `generated` dan filenya benar-benar ada.
