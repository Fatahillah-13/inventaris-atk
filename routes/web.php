<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMovementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,staff_pengelola'])->group(function () {
    Route::resource('items', ItemController::class);

    Route::get('stock', [StockMovementController::class, 'index'])->name('stock.index');
    Route::get('stock/masuk', [StockMovementController::class, 'createMasuk'])->name('stock.masuk.create');
    Route::post('stock/masuk', [StockMovementController::class, 'storeMasuk'])->name('stock.masuk.store');
    Route::get('stock/keluar', [StockMovementController::class, 'createKeluar'])->name('stock.keluar.create');
    Route::post('stock/keluar', [StockMovementController::class, 'storeKeluar'])->name('stock.keluar.store');
});

require __DIR__ . '/auth.php';
