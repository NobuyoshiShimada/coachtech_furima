<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AddressRequest;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/mypage/profile',[ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');
    Route::get('/purchase/{item}', [ItemController::class, 'purchase'])->name('item.purchase');
    Route::get('purchase/address/{item}', [ItemController::class, 'editAddress'])->name('address.edit');
    Route::post('purchase/address/{item}', [ItemController::class, 'updateAddress'])->name('address.update');
    Route::get('/?tab=mylist', function() {
    })->name('mylist');
    Route::post('/purchase/{item}', [ItemController::class, 'checkout'])->name('item.checkout');
    Route::get('purchase/success/{item}', [ItemController::class, 'success'])->name('purchase.success');
    Route::get('purchase/cancel/{item}', [ItemController::class, 'cancel'])->name('purchase.cancel');
});

Route::get('items/create',fn() => 'Listing Page')->name('items.create');

Route::get('/', [ItemController::class,'index'])->name('item.index');

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

Route::post('/items/{item}/comment', function () {
    return back();
})->name('comments.store');

Route::post('/login', function (LoginRequest $request) {
    return app(AuthenticatedSessionController::class)->store($request);
    });
