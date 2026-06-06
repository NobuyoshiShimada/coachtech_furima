<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ItemController extends Controller
{
    // 商品一覧
    public function index(Request $request) {
        $query = Item::query();

        if ($request->tab ==='mylist' && Auth::check()) {
            $user = Auth::user();

            $items = Item::whereHas('likes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'on_sale')->get();
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")->orWhere('description', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->tab === 'mylist' && Auth::check()) {
            $user = Auth::user();
            $query->whereHas('likes', function($q) use ($user){
                $q->where('user_id', $user->id);
            });
        }

        $items = $query->get();

        return view('index', compact('items'));
    }

    // 商品詳細画面
    public function show(Item $item) {
        $item->load(['condition', 'categories']);
        return view('show', compact('item'));
    }

    // 購入画面
    public function purchase(Item $item) {
        $user = Auth::user();
        if ($item->isSold()) {
            return redirect('/')->with('error', 'この商品は売り切れです');
        }
        return view ('purchase', compact('item', 'user'));
    }

    // 住所変更画面
    public function editAddress(Item $item) {
        $user = Auth::user();
        return view('address',compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item) {
        $user = Auth::user();
        $user->profile()->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ]);
        return redirect()->route('item.purchase', ['item' => $item->id]);
    }

    // stripeへ接続
    public function checkout(PurchaseRequest $request, Item $item) {
        $method = $request->input('payment_method');

        Stripe::setApiKey(config('services.stripe.secret'));

        $options = [];
        if ($method === 'konbini') {
            $options = [
                'konbini' => [
                    'expires_after_days' => 3,
                ],
            ];
        }

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => [$method],
            'payment_method_options' => $options,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url' => route('purchase.cancel', ['item' => $item->id]),
        ]);
        return redirect()->away($session->url);
    }

    public function success(Item $item) {
        $item->update(['status' => 'sold']);
        return redirect()->route('item.index')->with('message', '商品を購入しました');
    }

    public function cancel(Item $item) {
        return redirect()->route('item.purchase', ['item' => $item->id])->with('error', '決済がキャンセルされました');
    }

    // いいね機能
    public function toggleLike(Item $item) {
        $user = Auth::user();

        $like = $item->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // すでにいいねしていれば解除
            $like->delete();
            $isLiked = false;
        }
        else {
            // まだいいねしていなければ新規登録
            $item->likes()->create([
                'user_id' => $user->id
            ]);
            $isLiked = true;
        }

        return response()->json([
            'isLiked' => $isLiked,
            'likesCount' => $item->likes()->count()
        ]);
    }

    // コメント機能
    public function storeComment(CommentRequest $request, Item $item) {
        $user = Auth::user();

        $item->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', ['item' => $item->id])->with('message', 'コメントを投稿しました');
    }

    // 商品出品画面
    public function create() {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request) {
        $user = Auth::user();

        $path = &request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => $user->id,
            'condition_id' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand ?? '' ,
            'price' => $request->price,
            'description' => $request->description,
            'image_url' => asset('storage/' . $path),
            'status' => 'on_sale',
        ]);

        $item->categories()->attach($request->categories);

        return redirect()->route('mypage')->with('message', '商品を出品しました');
    }


}
