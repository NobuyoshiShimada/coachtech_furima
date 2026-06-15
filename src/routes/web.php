<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\CustomVerifyEmailController;
use Illuminate\Auth\Events\Login;

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

Route::middleware(['auth', 'verified'])->group(function() {

    Route::get('/mypage/profile',[ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    Route::get('/purchase/{item}', [ItemController::class, 'purchase'])->name('item.purchase');

    Route::get('purchase/address/{item}', [ItemController::class, 'editAddress'])->name('address.edit');
    Route::post('purchase/address/{item}', [ItemController::class, 'updateAddress'])->name('address.update');
    Route::post('/purchase/{item}', [ItemController::class, 'checkout'])->name('item.checkout');
    Route::get('purchase/success/{item}', [ItemController::class, 'success'])->name('purchase.success');
    Route::get('purchase/cancel/{item}', [ItemController::class, 'cancel'])->name('purchase.cancel');
    Route::post('/items/{item}/like', [ItemController::class, 'toggleLike'])->name('items.like');
    Route::post('/items/{item}/comment', [ItemController::class, 'storeComment'])->name('comments.store');
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');
});

Route::get('/', [ItemController::class,'index'])->name('item.index');

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// メール認証誘導画面（email.blade.php）
Route::get('/email/verify', function () {
    if (!session()->has('auth.verify.user_id')){
        return redirect()->route('item.index');
    }
     if (!auth()->check()) {
        auth()->loginUsingId(session('auth.verify.user_id'));
    }
    return view('auth.email');
})->name('verification.notice');

// メールのURLがクリックされたとき
Route::get('/email/verify/{id}/{hash}', CustomVerifyEmailController::class)
    ->middleware(['signed'])
    ->name('verification.verify');

// ログイン画面を表示する
Route::get('/login', function () {
    // Fortify::authenticateUsingで弾かれた未認証エラーを直接セッションから見抜きます。
    if (session()->get('errors') && session()->get('errors')->has('email')) {
        if (session()->get('errors')->first('email') === 'redirect_to_verify') {

            // 証拠を刻んで、メール認証誘導画面へ
            session(['auth.verify.redirect_now' => true]);
            return redirect()->route('verification.notice');
        }
    }

    if (session()->has('auth.verify.redirect_now')) {
        return redirect()->route('verification.notice');
    }
    return view('auth.login');
})->name('login');

// ログインボタンが押されたとき
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
