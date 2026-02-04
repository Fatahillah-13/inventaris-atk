<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AtkRequestController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\AtkShopRequestRekapController;

Route::get('/', function () {
    return view('landing');
});

// Route::get('/welcome-test', function () {
//     return view('welcome');
// });

Route::get('/register', function () {
    abort(404);
});

Route::get('/ajax/divisions-by-item/{item}', [LoanController::class, 'getDivisionsByItem'])
    ->name('ajax.divisions.byItem')
    ->middleware('throttle:20,1');

Route::get('/ajax/employee-by-nik/{nik}', [LoanController::class, 'getEmployeeByNik'])
    ->name('ajax.employee.byNik')
    ->middleware('throttle:10,1');

Route::get('/ajax/items-by-division/{division}', [StockMovementController::class, 'getItemsByDivision'])
    ->name('ajax.items.byDivision')
    ->middleware('throttle:20,1');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

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
// Route::get('permintaan_atk', [AtkRequestController::class, 'publicCreate'])
//     ->name('public.requests.create')
//     ->middleware('throttle:20,1');

// Route::post('permintaan_atk', [AtkRequestController::class, 'publicStore'])
//     ->name('public.requests.store')
//     ->middleware('throttle:10,1');

Route::middleware(['auth', 'role:admin,staff_pengelola,atk_master'])->group(function () {
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
    Route::post('/requests/{atkRequest}/approve', [AtkRequestController::class, 'approve'])
        ->name('requests.approve');
    Route::post('/requests/{atkRequest}/reject', [AtkRequestController::class, 'reject'])
        ->name('requests.reject');

    // Phase 1 ATK Request Workflow (catalog → cart → checkout → my requests)
    Route::prefix('permintaan-atk')->name('atk.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AtkCatalogController::class, 'catalog'])->name('catalog');
        Route::post('/cart/items', [\App\Http\Controllers\AtkCatalogController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [\App\Http\Controllers\AtkCatalogController::class, 'viewCart'])->name('cart');
        Route::patch('/cart/items/{atkShopRequestItem}', [\App\Http\Controllers\AtkCatalogController::class, 'updateCartItem'])->name('cart.update');
        Route::delete('/cart/items/{atkShopRequestItem}', [\App\Http\Controllers\AtkCatalogController::class, 'removeCartItem'])->name('cart.remove');
        Route::post('/checkout', [\App\Http\Controllers\AtkCatalogController::class, 'checkout'])->name('checkout');
        Route::get('/requests', [\App\Http\Controllers\AtkCatalogController::class, 'myRequests'])->name('my-requests');
        Route::get('/requests/{atkShopRequest}', [\App\Http\Controllers\AtkCatalogController::class, 'showRequest'])->name('show');
    });
});

// Phase 2 ATK Master Approval Workflow
Route::middleware(['auth', 'role:atk_master'])->group(function () {
    Route::prefix('atk-master/permintaan-atk')->name('atk-master.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AtkMasterController::class, 'index'])->name('index');
        Route::get('/request_list', [\App\Http\Controllers\AtkMasterController::class, 'requestList'])->name('requestList');
        Route::get('/{atkShopRequest}', [\App\Http\Controllers\AtkMasterController::class, 'show'])->name('show');
        Route::post('/{atkShopRequest}/approve', [\App\Http\Controllers\AtkMasterController::class, 'approve'])->name('approve');
        Route::post('/{atkShopRequest}/reject', [\App\Http\Controllers\AtkMasterController::class, 'reject'])->name('reject');
        Route::post('/{atkShopRequest}/ready_to_pickup', [\App\Http\Controllers\AtkMasterController::class, 'readyToPickup'])->name('ready_to_pickup');
        Route::post('/{atkShopRequest}/finish', [\App\Http\Controllers\AtkMasterController::class, 'finish'])->name('finish');
        Route::get('/rekap/index', [AtkShopRequestRekapController::class, 'index'])->name('atkmaster.rekap');
        Route::get('/rekap/export', [AtkShopRequestRekapController::class, 'exportExcel'])->name('atkmaster.rekap.excel');
    });
});

// Route::prefix('atk-master/permintaan-atk')->middleware(['auth', 'role:atk_master'])->group(function () {});

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

    // Manajemen kategori barang
    Route::resource('item-categories', ItemCategoryController::class)->except(['show']);
});
