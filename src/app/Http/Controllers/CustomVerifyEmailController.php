<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class CustomVerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect('/login');
        }

        // すでに認証済みなら、そのままプロフィール画面へ
        if ($user->hasVerifiedEmail()) {
            auth()->login($user);
            return redirect('/mypage/profile');
        }

        // 未ログイン状態でも、ここで厳密にメール認証を完了（email_verified_atを記録）させます！
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        auth()->login($user);
        session()->forget(['auth.verify.user_id', 'auth.verify.redirect_now']);

        return redirect('/mypage/profile');
    }
}
