<?php

namespace Database\Seeders;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database dengan data demo. Semua nama peserta
     * di bawah ini sintetis (bukan orang sungguhan), hanya untuk mengisi
     * data contoh agar alur aplikasi bisa langsung dicoba.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Panitia Demo',
            'email' => 'panitia@kampus.test',
            'password' => bcrypt('password'),
        ]);

        $this->command?->info("Admin demo dibuat: {$admin->email} / password");

        // Acara 1: sudah selesai, sudah ada yang check-in -> siap untuk demo
        // generate sertifikat.
        $pastEvent = Event::create([
            'name' => 'Workshop Manajemen Proyek untuk Mahasiswa',
            'description' => 'Pelatihan dasar manajemen proyek: perencanaan, eksekusi, dan evaluasi, ditujukan untuk mahasiswa tingkat akhir dan organisasi kemahasiswaan.',
            'location' => 'Aula Fakultas Teknik, Gedung B Lantai 3',
            'starts_at' => now()->subDays(5)->setTime(9, 0),
            'ends_at' => now()->subDays(5)->setTime(12, 0),
            'quota' => 50,
        ]);

        $pastAttendees = [
            ['Nadia Ramadhani', true],
            ['Farhan Hidayat', true],
            ['Kartika Sari Dewi', true],
            ['Yusuf Maulana', true],
            ['Putri Ayu Lestari', false],
            ['Bagus Prakoso', false],
        ];

        foreach ($pastAttendees as [$name, $checkedIn]) {
            $code = Registration::generateUniqueCode();
            $slug = str()->slug($name);

            Registration::create([
                'event_id' => $pastEvent->id,
                'name' => $name,
                'email' => "{$slug}@mahasiswa-demo.test",
                'phone' => '0812'.random_int(10000000, 99999999),
                'registration_code' => $code,
                'qr_payload' => $code,
                'status' => $checkedIn ? RegistrationStatus::CheckedIn : RegistrationStatus::Registered,
                'checked_in_at' => $checkedIn ? now()->subDays(5)->setTime(8, random_int(45, 59)) : null,
            ]);
        }

        // Acara 2: masih akan datang -> siap untuk demo pendaftaran publik
        // dan check-in di lokasi.
        $upcomingEvent = Event::create([
            'name' => 'Seminar Keamanan Data untuk Organisasi Kampus',
            'description' => 'Seminar pengenalan praktik keamanan data dasar untuk pengurus organisasi kemahasiswaan yang mengelola data anggota.',
            'location' => 'Ruang Seminar Perpustakaan Pusat',
            'starts_at' => now()->addDays(10)->setTime(13, 0),
            'ends_at' => now()->addDays(10)->setTime(16, 0),
            'quota' => 30,
        ]);

        $upcomingAttendees = ['Dimas Anggara', 'Salsabila Putri', 'Reza Firmansyah'];

        foreach ($upcomingAttendees as $name) {
            $code = Registration::generateUniqueCode();
            $slug = str()->slug($name);

            Registration::create([
                'event_id' => $upcomingEvent->id,
                'name' => $name,
                'email' => "{$slug}@mahasiswa-demo.test",
                'phone' => '0812'.random_int(10000000, 99999999),
                'registration_code' => $code,
                'qr_payload' => $code,
                'status' => RegistrationStatus::Registered,
            ]);
        }

        $this->command?->info("Acara demo dibuat: \"{$pastEvent->name}\" (selesai) dan \"{$upcomingEvent->name}\" (akan datang).");
    }
}
