<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller

{
    public function edit()
    {
        $user = Auth()->user();

        // まだ未登録の場合
            return view('profile', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 1.usersテーブル(ユーザー名)の更新
        $user->update([
            'name' => $request->name
        ]);

        // 2.profilesテーブルの更新データ
        $profileData = [
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ];

        // 3.画像がアップされた場合の保存処理
        if($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $profileData['image_url'] = $path;
        }

        // 4.profilesテーブルのレコードを更新
        $user->profile()->update($profileData);

        // 5.更新完了後にトップページへリダイレクト
        return redirect('/')->with('message', 'プロフィールを更新しました');
    }

    public function mypage(Request $request) {
        $user = Auth::user();

        $items = Item::where('user_id', $user->id)->get();

        if ($request->tab === 'buy') {
            $items = Item::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }
        return view('mypage', compact('user', 'items'));
    }
}


