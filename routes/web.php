<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AtkRequestController;
use App\Http\Controllers\DivisionController;

Route::get('/', function () {
    return view('landing');
});

// Route::get('/welcome-test', function () {
//     return view('welcome');
// });

Route::get('/register', function () {
    abort(404);
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// PUBLIC FORM PEMINJAMAN (TANPA LOGIN)
Route::get('peminjaman', [LoanController::class, 'publicCreate'])
    ->name('public.loans.create')
    ->middleware('throttle:20,1'); // max 20 request per menit per IP (contoh)

Route::post('peminjaman', [LoanController::class, 'publicStore'])
    ->name('public.loans.store')
    ->middleware('throttle:10,1'); // batasi submit

// FORM PERMINTAAN ATK (TANPA LOGIN)
Route::get('permintaan-atk', [AtkRequestController::class, 'publicCreate'])
    ->name('public.requests.create')
    ->middleware('throttle:20,1');

Route::post('permintaan-atk', [AtkRequestController::class, 'publicStore'])
    ->name('public.requests.store')
    ->middleware('throttle:10,1');

Route::middleware(['auth', 'role:admin,staff_pengelola'])->group(function () {
    Route::resource('items', ItemController::class);

    // catatan barang masuk/keluar
    Route::get('stock', [StockMovementController::class, 'index'])->name('stock.index');
    Route::get('stock/masuk', [StockMovementController::class, 'createMasuk'])->name('stock.masuk.create');
    Route::post('stock/masuk', [StockMovementController::class, 'storeMasuk'])->name('stock.masuk.store');
    Route::get('stock/keluar', [StockMovementController::class, 'createKeluar'])->name('stock.keluar.create');
    Route::post('stock/keluar', [StockMovementController::class, 'storeKeluar'])->name('stock.keluar.store');

    // daftar peminjaman, pengembalian, dll
    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('loans/{loan}/return', [LoanController::class, 'returnLoan'])->name('loans.return');

    // Daftar permintaan ATK (internal)
    Route::get('requests', [AtkRequestController::class, 'index'])->name('requests.index');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Manajemen divisi
    Route::resource('divisions', DivisionController::class)->except(['show']);
});
