<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PasokController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PosKasirController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------
// Publik (toko online) - keranjang boleh diisi tanpa login
// ---------------------------------------------------------------
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/toko/{buku}', [LandingController::class, 'show'])->name('toko.show');

Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/{buku}', [KeranjangController::class, 'add'])->name('keranjang.add');
Route::patch('/keranjang/{buku}', [KeranjangController::class, 'update'])->name('keranjang.update');
Route::delete('/keranjang/{buku}', [KeranjangController::class, 'remove'])->name('keranjang.remove');

// ---------------------------------------------------------------
// Autentikasi
// ---------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---------------------------------------------------------------
// Login diperlukan
// ---------------------------------------------------------------
Route::middleware('auth')->group(function () {
    // Semua role
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Pelanggan: checkout & pesanan
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:3,1');

        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{penjualan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::post('/pesanan/{penjualan}/bukti-pembayaran', [PesananController::class, 'uploadPaymentProof'])->name('pesanan.proof.upload')->middleware('throttle:10,1');
        Route::get('/pesanan/{penjualan}/bukti-pembayaran', [PesananController::class, 'paymentProof'])->name('pesanan.proof');
        Route::get('/pesanan/{penjualan}/bukti-penerimaan', [PesananController::class, 'deliveryProof'])->name('pesanan.delivery-proof');
        Route::post('/pesanan/{penjualan}/batal', [PesananController::class, 'cancel'])->name('pesanan.cancel');
    });

    // Staf (kasir & admin)
    Route::middleware('role:kasir,admin')->group(function () {
        Route::get('/pos', [PosKasirController::class, 'index'])->name('pos.index');
        Route::post('/pos/proses', [PosKasirController::class, 'process'])->name('pos.process')->middleware('throttle:20,1');

        Route::get('/kasir/dashboard', [DashboardController::class, 'kasir'])->name('kasir.dashboard');

        // Pesanan dari toko online dapat dikelola oleh kasir maupun admin.
        Route::get('/pesanan-masuk', [PenjualanController::class, 'onlineIndex'])->name('pesanan-masuk.index');
        Route::get('/pesanan-masuk/{penjualan}', [PenjualanController::class, 'onlineShow'])->name('pesanan-masuk.show');
        Route::get('/pesanan-masuk/{penjualan}/bukti-pembayaran', [PenjualanController::class, 'onlinePaymentProof'])->name('pesanan-masuk.proof');
        Route::post('/pesanan-masuk/{penjualan}/status', [PenjualanController::class, 'updateOnlineStatus'])->name('pesanan-masuk.status')->middleware('throttle:20,1');
        Route::post('/pesanan-masuk/{penjualan}/assign-kurir', [PenjualanController::class, 'assignCourier'])->name('pesanan-masuk.assign-kurir')->middleware('throttle:20,1');
    });

    // Dashboard dan tugas khusus kurir. Setiap pesanan diverifikasi kepemilikannya oleh controller.
    Route::middleware('role:kurir')->prefix('kurir')->name('kurir.')->group(function () {
        Route::get('/dashboard', [KurirController::class, 'index'])->name('dashboard');
        Route::get('/pesanan/{penjualan}', [KurirController::class, 'show'])->name('pesanan.show');
        Route::post('/pesanan/{penjualan}/status', [KurirController::class, 'updateStatus'])->name('pesanan.status')->middleware('throttle:20,1');
    });

    // Pengaturan sensitif toko hanya boleh dikelola admin.
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('setting.index');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('setting.update');
    });

    // Khusus admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        Route::resource('buku', BukuController::class);
        Route::resource('distributor', DistributorController::class)->only(['index', 'store']);
        Route::resource('user', UserController::class)->only(['index', 'store', 'destroy']);
        Route::resource('pasok', PasokController::class)->only(['index', 'create', 'store']);
        Route::resource('penjualan', PenjualanController::class)->only(['index', 'show']);
        Route::post('/penjualan/{penjualan}/status', [PenjualanController::class, 'updateStatus'])->name('penjualan.status');

        // Laporan
        Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
        Route::get('/laporan/pasok', [LaporanController::class, 'pasok'])->name('laporan.pasok');
        Route::get('/laporan/inventori', [LaporanController::class, 'inventori'])->name('laporan.inventori');
        Route::get('/laporan/pendapatan', [LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
    });
});
