<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ItemController extends Controller
{
    public function index(Request $request) {
        $items = Item::where('status', 'on_sale')->get();
        $query = Item::where('status', 'on_sale');

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

    public function show(Item $item) {
        $item->load(['condition', 'categories']);
        return view('show', compact('item'));
    }

    public function purchase(Item $item) {
        $user = Auth::user();
        if ($item->isSold()) {
            return redirect('/')->with('error', 'この商品は売り切れです');
        }
        return view ('purchase', compact('item', 'user'));
    }

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


}
