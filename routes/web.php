<?php

use App\Http\Controllers\OwnerPortalController;
use App\Http\Controllers\RekapKeuanganController;
use App\Http\Controllers\AiAssistantController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

// Rute Autentikasi & Lupa Sandi (Bisa diakses tanpa login)
Route::get('/login', [OwnerPortalController::class, 'showLogin'])->name('login');
Route::post('/login', [OwnerPortalController::class, 'prosesLogin'])->name('login.proses');
Route::post('/logout', [OwnerPortalController::class, 'logout'])->name('logout');

Route::get('/lupa-password', [OwnerPortalController::class, 'formLupaPassword'])->name('password.request');
Route::post('/lupa-password', [OwnerPortalController::class, 'kirimLinkReset'])->name('password.email');
Route::get('/reset-password/{token}', [OwnerPortalController::class, 'formResetPassword'])->name('password.reset');
Route::post('/reset-password', [OwnerPortalController::class, 'prosesResetPassword'])->name('password.update');

// Rute yang Membutuhkan Login (Middleware Auth)
Route::middleware('auth')->group(function () {
    Route::get('/beranda', [OwnerPortalController::class, 'beranda'])->name('beranda');

    // Laporan Rekap (CSV & PDF)
    Route::get('/rekap/csv', [RekapKeuanganController::class, 'exportCsv'])->name('rekap.csv');
    Route::get('/rekap/pdf', [OwnerPortalController::class, 'cetakPdf'])->name('rekap.pdf');

    // Kelola Produk
    Route::post('/produk', [OwnerPortalController::class, 'simpanProduk'])->name('produk.simpan');
    Route::put('/produk/{produk}', [OwnerPortalController::class, 'updateProduk'])->name('produk.update');
    Route::delete('/produk/{produk}', [OwnerPortalController::class, 'hapusProduk'])->name('produk.hapus');
    Route::post('/produk/{produk}/restock', [OwnerPortalController::class, 'tambahStok'])->name('produk.restock');

    // Kelola Kategori
    Route::post('/kategori', [OwnerPortalController::class, 'simpanKategori'])->name('kategori.simpan');
    Route::delete('/kategori/{id}', [OwnerPortalController::class, 'hapusKategori'])->name('kategori.hapus');

    // Penjualan & AI
    Route::post('/penjualan', [OwnerPortalController::class, 'simpanPenjualan'])->name('penjualan.simpan');
    Route::post('/ai/tanya', [AiAssistantController::class, 'tanyaAi'])->name('ai.tanya');
});