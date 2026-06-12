<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    // 10.商品購入

    /** @test */
    public function 商品購入_購入するボタンを押下すると購入が完了する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'status' => 'on_sale',
            ]);

        $response = $this->actingAs($user)->get("purchase/success/{$item->id}");

        $response->assertRedirect(route('item.index'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
        $this->assertEquals('sold',$item->fresh()->status);
    }

    /** @test */
    public function 商品購入_購入した商品は商品一覧画面にてSoldと表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '売り切れた商品',
            'status' => 'sold',
            ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    /** @test */
    public function 商品購入_プロフィール購入した商品一覧に追加される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '購入した商品',
            'status' => 'on_sale',
            ]);

        $this->actingAs($user)->get("purchase/success/{$item->id}");

        $response = $this->actingAs($user)->get('/mypage?tab=buy');

        $response->assertStatus(200);
        $response->assertSee('購入した商品');
    }

    // 11.支払い方法選択

    /** @test */
    public function 支払い方法選択_小計画面で変更が反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['price' => 5000]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        $checkoutResponse = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini'
        ]);

        $checkoutResponse->assertStatus(302);
    }
}
