<?php

use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. IMPORT SEMUA CONTROLLER YANG DIGUNAKAN
// PASTIKAN SEMUA NAMA CLASS DAN NAMESPACE SUDAH BENAR
// =========================================================================
use App\Http\Controllers\KueController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PencatatanPOController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Di sini Anda dapat mendaftarkan route web untuk aplikasi Anda. Route ini
| dimuat oleh RouteServiceProvider dalam grup yang berisi middleware "web".
|
*/

// =========================================================================
// 2. ROUTE UTAMA (HOME)
// =========================================================================

// Mengarahkan halaman utama (/) langsung ke daftar kue (atau dashboard)
Route::get('/', [KueController::class, 'index'])->name('home');

// =========================================================================
// 3. ROUTE RESOURCE (CRUD MASTER)
// Menggunakan Route::resource untuk 7 method CRUD sekaligus (index, create, 
// store, show, edit, update, destroy).
// =========================================================================

// CRUD Kue
Route::resource('kues', KueController::class);

// CRUD Pembeli
Route::resource('pembelis', PembeliController::class);

// CRUD Bahan Baku
Route::resource('bahan_bakus', BahanBakuController::class);

// =========================================================================
// 4. ROUTE RESOURCE LOGIKA BISNIS (TRANSAKSI & PO)
// =========================================================================

// Transaksi Penjualan (Header & Detail)
Route::resource('transaksis', TransaksiController::class);

// Pencatatan Purchase Order (PO)
Route::resource('pencatatan_pos', PencatatanPOController::class);

// =========================================================================
// 5. ROUTE KUSTOM
// Diperlukan untuk method "complete" (Menyelesaikan PO dan Update Stok)
// =========================================================================

// Route POST untuk menyelesaikan PO
Route::post('pencatatan_pos/{pencatatan_po}/complete', [PencatatanPOController::class, 'complete'])
     ->name('pencatatan_pos.complete');