<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\WorkerController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:manager'])->prefix('manager')->group(function () {
        // 使用者管理
        Route::get('/users', [ManagerController::class, 'userIndex'])->name('manager.users');
        Route::post('/users', [ManagerController::class, 'userStore'])->name('manager.users.store');
        Route::delete('/users/{user}', [ManagerController::class, 'userDestroy'])->name('manager.users.destroy');

        // 產品管理
        Route::get('/products', [ManagerController::class, 'productIndex'])->name('manager.products');
        Route::post('/products', [ManagerController::class, 'productStore'])->name('manager.products.store');
        Route::get('/products/{product}/edit', [ManagerController::class, 'productEdit'])->name('manager.products.edit');
        Route::put('/products/{product}', [ManagerController::class, 'productUpdate'])->name('manager.products.update');
        Route::delete('/products/{product}', [ManagerController::class, 'productDestroy'])->name('manager.products.destroy');

        // 製程選項管理
        Route::get('/process_types', [ManagerController::class, 'processTypeIndex'])->name('manager.process_types');
        Route::post('/process_types', [ManagerController::class, 'processTypeStore'])->name('manager.process_types.store');
        Route::delete('/process_types/{processType}', [ManagerController::class, 'processTypeDestroy'])->name('manager.process_types.destroy');

        Route::get('/progress', [ManagerController::class, 'progressIndex'])->name('manager.progress');
    });

    Route::middleware(['role:worker'])->prefix('worker')->group(function () {
        Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('worker.dashboard');
        Route::post('/product-process/{id}/start', [WorkerController::class, 'startProcess'])->name('worker.process.start');
        Route::post('/product-process/{id}/complete', [WorkerController::class, 'completeProcess'])->name('worker.process.complete');
        Route::post('/product-process/{id}/rollback', [WorkerController::class, 'rollbackProcess'])->name('worker.process.rollback');
    });
});
