<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\RsvpController;
use App\Http\Controllers\Admin\WeddingController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin dashboard (if logged in) or login
Route::get('/', fn () => redirect()->route('admin.dashboard'));

// Public invitation
Route::get('/invite/{token}', [InviteController::class, 'show'])->name('invite.show');
Route::post('/invite/{token}/rsvp', [InviteController::class, 'rsvp'])->name('invite.rsvp');

// Admin panel
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/wedding', [WeddingController::class, 'edit'])->name('wedding');
    Route::post('/wedding', [WeddingController::class, 'update'])->name('wedding.update');

    Route::get('/guests', [GuestController::class, 'index'])->name('guests');
    Route::post('/guests/import', [GuestController::class, 'import'])->name('guests.import');
    Route::get('/guests/export', [GuestController::class, 'export'])->name('guests.export');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

    Route::get('/rsvp', [RsvpController::class, 'index'])->name('rsvp');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
