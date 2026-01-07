<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRCodeController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public route - Xem thông tin sản phẩm qua QR
Route::get('products/{id}/info', [QRCodeController::class, 'info'])->name('products.info');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class)->except(['create', 'edit', 'show']);
    Route::get('products/{id}/qrcode', [QRCodeController::class, 'generate'])->name('products.qrcode');
    Route::get('products/{id}/qrcode-image', [QRCodeController::class, 'generateImage'])->name('products.qrcode.image');
    Route::get('products-export-excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('products-export-pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    
    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit', 'show']);
    
    // Warehouses
    Route::resource('warehouses', WarehouseController::class)->except(['create', 'edit', 'show']);
    
    // Stock In
    Route::get('stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::post('stock-in', [StockInController::class, 'store'])->name('stock-in.store');
    Route::get('stock-in/{stockIn}', [StockInController::class, 'show'])->name('stock-in.show');
    Route::get('stock-in/{stockIn}/export-excel', [StockInController::class, 'exportExcel'])->name('stock-in.export.excel');
    Route::get('stock-in/{stockIn}/export-pdf', [StockInController::class, 'exportPdf'])->name('stock-in.export.pdf');
    
    // Stock Out
    Route::get('stock-out', [StockOutController::class, 'index'])->name('stock-out.index');
    Route::post('stock-out', [StockOutController::class, 'store'])->name('stock-out.store');
    Route::get('stock-out/{stockOut}', [StockOutController::class, 'show'])->name('stock-out.show');
    Route::get('stock-out/{stockOut}/export-excel', [StockOutController::class, 'exportExcel'])->name('stock-out.export.excel');
    Route::get('stock-out/{stockOut}/export-pdf', [StockOutController::class, 'exportPdf'])->name('stock-out.export.pdf');
    
    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/export-excel', [InventoryController::class, 'exportExcel'])->name('inventory.export.excel');
    Route::get('inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.export.pdf');
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Users (Admin only)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
