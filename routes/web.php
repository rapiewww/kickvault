<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'customer'
            ? redirect()->route('customer.catalog')
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Route yang Wajib Login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // KHUSUS CUSTOMER (PELANGGAN)
    // ==========================================
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/catalog', [CustomerController::class, 'catalog'])->name('catalog');
        Route::post('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
        Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('/orders/{sale}', [CustomerController::class, 'orderShow'])->name('orders.show');
    });

    // ==========================================
    // INTERNAL (ADMIN & STAFF)
    // ==========================================
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ==========================================
        // KHUSUS ADMIN (Dilindungi di Server)
        // ==========================================
        Route::middleware('role:admin')->group(function () {
            // Master Data Kategori & Supplier (Full CRUD hanya Admin)
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('suppliers', SupplierController::class)->except(['show']);

            // Master Data Produk (Tambah, Edit, Hapus hanya Admin)
            // Penting: Daftarkan sebelum route index/show agar '/products/create' tidak dianggap sebagai {product}
            Route::resource('products', ProductController::class)->except(['index', 'show']);

            // Pembatalan Transaksi (Slide 5 & 8: Hanya Admin yang boleh membatalkan transaksi)
            Route::patch('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
            Route::patch('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

            // Manajemen Pengguna & Role (Slide 5 & 13: Hanya Admin)
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Staff & Admin boleh melihat daftar dan detail produk (Slide 5)
        Route::resource('products', ProductController::class)->only(['index', 'show']);

        // Transaksi Pembelian (Stock-In) - Tambah 'show' untuk halaman detail (Slide 13)
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Transaksi Penjualan (Stock-Out) - Tambah 'show' untuk halaman detail (Slide 13)
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    });
});
