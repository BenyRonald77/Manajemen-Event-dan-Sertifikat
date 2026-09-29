<?php

use App\Http\Controllers\CertificateDownloadController;
use App\Livewire\Admin\CheckinScanner;
use App\Livewire\Admin\EventIndex;
use App\Livewire\Admin\EventShow;
use App\Livewire\Public\CertificateVerify;
use App\Livewire\Public\EventList;
use App\Livewire\Public\EventRegister;
use App\Livewire\Public\RegistrationConfirmation;
use App\Livewire\Public\RegistrationLookup;
use Illuminate\Support\Facades\Route;

// Publik: tidak perlu login.
Route::get('/', EventList::class)->name('home');
Route::get('/acara', EventList::class)->name('events.public-index');
Route::get('/acara/{event}/daftar', EventRegister::class)->name('events.register');
Route::get('/pendaftaran/{registration_code}/konfirmasi', RegistrationConfirmation::class)->name('registrations.confirmation');
Route::get('/pendaftaran/cek', RegistrationLookup::class)->name('registrations.lookup');
Route::get('/sertifikat/verifikasi/{token}', CertificateVerify::class)->name('certificates.verify');
Route::get('/sertifikat/verifikasi/{token}/unduh', CertificateDownloadController::class)->name('certificates.download');

// Panitia/admin: wajib login.
Route::middleware(['auth', 'verified'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('acara', EventIndex::class)->name('events.index');
    Route::get('acara/{event}', EventShow::class)->name('events.show');
    Route::get('checkin', CheckinScanner::class)->name('checkin');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
