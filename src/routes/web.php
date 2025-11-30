<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PricesController;
use App\Http\Controllers\RuteController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'login'])->name('login');
    Route::get('/home', [LoginController::class, 'index'])->name('index')->middleware('auth');
    Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin')->middleware('web');
    Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');
});

Route::prefix('register')->middleware('auth')->group(function () {
    Route::get('/', [RegisterController::class, 'register'])->name('register');
    Route::post('actionregister', [RegisterController::class, 'actionregister'])->name('actionregister');
    Route::get('editregister', [RegisterController::class, 'editregister'])->name('editregister');
    Route::post('updateregister', [RegisterController::class, 'updateregister'])->name('updateregister');
    Route::get('listregister', [RegisterController::class, 'listregister'])->name('listregister');
    Route::get('filter_register', [RegisterController::class, 'filter_register'])->name('filter_register');
    Route::get('edit_list_register/{id}', [RegisterController::class, 'edit_list_register'])->name('edit_list_register');
    Route::get('select_list_register_staff/{id}', [RegisterController::class, 'select_list_register_staff'])->name('select_list_register_staff');
    Route::post('update_list_register', [RegisterController::class, 'update_list_register'])->name('update_list_register');
    Route::delete('delete_list_register/{id}', [RegisterController::class, 'delete_list_register'])->name('delete_list_register');
    Route::get('/generate-user-id', [RegisterController::class, 'generate_user_id'])->name('generate_user_id');
});

Route::prefix('customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index_customer'])->name('index_customer');

    // DataTables
    Route::get('/customer_get_data', [CustomerController::class, 'customer_get_data'])->name('customer_get_data');

    // CRUD
    Route::post('/store', [CustomerController::class, 'customer_store'])->name('customer_store');
    Route::get('/show/{id}', [CustomerController::class, 'customer_show'])->name('customer_show');
    Route::post('/update/{id}', [CustomerController::class, 'customer_update'])->name('customer_update');
    Route::post('/destroy/{id}', [CustomerController::class, 'customer_destroy'])->name('customer_destroy');

    // Callback
    Route::get('/customer_kode', [CustomerController::class, 'customer_kode'])->name('customer_kode');
});

Route::prefix('kendaraan')->group(function () {
    Route::get('/', [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::post('/store', [KendaraanController::class, 'store'])->name('kendaraan.store');
    Route::get('/data', [KendaraanController::class, 'data'])->name('kendaraan.data');
    Route::get('/edit/{id}', [KendaraanController::class, 'edit'])->name('kendaraan.edit');
    Route::post('/update/{id}', [KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::post('/delete/{id}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy');
    // Callback
    Route::get('/kendaraan_kode', [KendaraanController::class, 'kendaraan_kode'])->name('kendaraan_kode');
});

Route::prefix('driver')->group(function () {
    Route::get('/', [DriverController::class, 'index'])->name('driver.index');
    Route::get('/data', [DriverController::class, 'data'])->name('driver.data');
    Route::get('/edit/{id}', [DriverController::class, 'edit'])->name('driver.edit');
    Route::post('/store', [DriverController::class, 'store'])->name('driver.store');
    Route::post('/update/{id}', [DriverController::class, 'update'])->name('driver.update');
    Route::post('/destroy/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');
    // Callback
    Route::get('/driver_kode', [DriverController::class, 'driver_kode'])->name('driver_kode');
});

Route::prefix('price-expedition')->group(function() {
    Route::get('/', [PricesController::class, 'index'])->name('price-expedition.index');
    Route::get('/data', [PricesController::class, 'getData'])->name('price-expedition.data');
    Route::post('/store', [PricesController::class, 'store'])->name('price-expedition.store');
    Route::get('/show/{id}', [PricesController::class, 'show'])->name('price-expedition.show');
    Route::post('/update/{id}', [PricesController::class, 'update'])->name('price-expedition.update');
    Route::post('/destroy/{id}', [PricesController::class, 'destroy'])->name('price-expedition.destroy');
});

Route::prefix('rute')->group(function() {
    Route::get('/data', [RuteController::class, 'getData'])->name('rute.data');
    Route::post('/store', [RuteController::class, 'store'])->name('rute.store');
    Route::get('/show/{id}', [RuteController::class, 'show'])->name('rute.show');
    Route::post('/update/{id}', [RuteController::class, 'update'])->name('rute.update');
    Route::post('/destroy/{id}', [RuteController::class, 'destroy'])->name('rute.destroy');
});


// Route::prefix('register')->group(function () {
//     Route::get('/users', UsersPage::class)->name('users.page');
// });

