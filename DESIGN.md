# DESIGN.md

Arah desain untuk aplikasi Manajemen Event dan Sertifikat.

## Apa ini

Ini adalah alat kerja internal untuk panitia event (admin/staff) dan alat verifikasi publik untuk sertifikat, bukan situs marketing. Tidak ada halaman penjualan, tidak ada testimoni, tidak ada statistik yang dipajang untuk menarik perhatian. Setiap layar dibangun untuk satu pekerjaan yang jelas: mendaftar, memindai, memantau proses, atau memverifikasi.

Reading this as: internal event-ops admin tool untuk panitia dan alat verifikasi publik untuk pihak ketiga, gaya visual netral-fungsional seperti alat kerja pemerintahan/enterprise (GOV.UK, Linear), dial ENERGY 2 / RHYTHM 2 / MOTION 1.

Dial: ENERGY 2 / RHYTHM 2 / MOTION 1

- **ENERGY 2**: alat ini dipakai berulang setiap hari oleh panitia, jadi tampilannya harus jelas dan cukup percaya diri (bukan sterile/hambar seperti dial 1), tapi tetap tenang karena ini bukan produk yang harus "menjual diri". Referensi rasa: Stripe dashboard, Linear.
- **RHYTHM 2**: sebagian besar layar mengikuti pola konsisten (form, tabel, halaman detail) karena ini alat operasional yang harus terasa dapat diprediksi, tapi ada variasi yang disengaja di titik penting: halaman konfirmasi pendaftaran (fokus pada QR besar), halaman progres batch (fokus pada angka progres), halaman verifikasi publik (fokus pada status ASLI/tidak).
- **MOTION 1**: hanya hover/focus state dan transisi progress bar yang berjalan (karena datanya memang berubah live lewat polling). Tidak ada scroll-reveal atau animasi dekoratif, karena ini alat kerja yang dibuka berulang, bukan halaman yang dijelajahi sekali.

## Palet warna

Netral (basis) + satu aksen.

- **Netral**: skala abu-abu/slate (Tailwind `slate` 50 sampai 900) untuk teks, latar, border, dan struktur. Alasan: ini alat administratif yang dibaca lama (tabel peserta, log check-in), skala netral menjaga mata tidak lelah dan membuat hierarki datang dari tipografi/spacing, bukan dari warna.
- **Aksen: `teal` (Tailwind `teal-600`/`teal-700`)**. Dipakai hanya untuk aksi utama (tombol submit, tombol "Generate Sertifikat", link status "sudah check-in", badge status "ASLI/valid") dan tidak dipakai untuk dekorasi. Alasan pemilihan: teal cukup jauh dari biru-ungu default AI (R-01) dan dari merah/hijau semantik (dipakai khusus untuk error/sukses), sehingga aksen ini tidak bertabrakan dengan warna status (merah untuk gagal/ditolak, hijau untuk berhasil/check-in). Satu warna aksen ini konsisten di semua halaman: publik, admin, dan verifikasi.
- **Warna status semantik** (bukan bagian dari aksen, dipakai fungsional): hijau (`emerald-600`) untuk berhasil/check-in/valid, merah (`red-600`) untuk gagal/ditolak/tidak valid, kuning (`amber-600`) untuk pending/menunggu proses.

## Tipografi

Font default Breeze/Tailwind (sistem font stack / Figtree bawaan Breeze). Alasan: ini alat kerja yang dibaca dalam tabel dan form padat, font default sudah punya keterbacaan tinggi di ukuran kecil dan tidak butuh personality font khusus untuk alat internal. Tidak ada heading monospace atau uppercase dengan letter-spacing lebar.

## Layout

- Tidak ada bento grid, tidak ada hero marketing, tidak ada "how it works" 3 langkah dekoratif.
- Halaman admin: sidebar/navbar sederhana (Breeze default) + konten dengan struktur tabel/kartu status yang mengikuti kebutuhan data (bukan 4 kartu statistik seragam tanpa makna).
- Halaman publik (form daftar, konfirmasi, verifikasi): satu kolom terfokus, tanpa navbar admin, karena pengunjung publik tidak perlu melihat menu internal.
- Radius kecil-konsisten (Tailwind default `rounded-md`/`rounded-lg`), tidak semua elemen pill-shape.
- Shadow dipakai tipis hanya pada kartu yang perlu menonjol dari latar (misal kartu QR di halaman konfirmasi), bukan default di semua elemen.

## Ikon

Tidak memakai ikon generik AI (sparkle, magic, robot). Kalau butuh ikon (misal ikon kamera di scanner, ikon unduh di sertifikat), pakai ikon fungsional yang relevan langsung dengan aksinya, bukan dekorasi.

## Yang sengaja tidak ada

Tidak ada testimoni, tidak ada angka statistik yang dipajang ("500+ event terselenggara" dsb), tidak ada ilustrasi. Semua angka yang ditampilkan (progres batch, jumlah peserta, jumlah check-in) adalah angka nyata dari database, bukan hiasan.
