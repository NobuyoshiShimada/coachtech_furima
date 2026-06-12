<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    // 5.マイリスト一覧取得

    /** @test */
    public function マイリスト_いいねした商品だけが表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myLikedItem = Item::factory()->create(['name' => '私がいいねした商品']);
        $notLikedItem = Item::factory()->create(['name' => 'いいねしていない商品']);
        $otherLikedItem = Item::factory()->create(['name' => '他人がいいねした商品']);

        $myLikedItem->likes()->create(['user_id' => $user->id]);
        $otherLikedItem->likes()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('私がいいねした商品');
        $response->assertDontSee('いいねしていない商品');
        $response->assertDontSee('他人がいいねした商品');
    }

    /** @test */
    public function マイリスト_購入済み商品は_Sold_と表示される()
    {
        $user = User::factory()->create();
        $onSaleItem = Item::factory()->create(['name' => 'まだ買える商品', 'status' => 'on_sale']);
        $soldItem = Item::factory()->create(['name' => '売り切れた商品', 'status' => 'sold']);

        $onSaleItem->likes()->create(['user_id' => $user->id]);
        $soldItem->likes()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('まだ買える商品');
        $response->assertSee('売り切れた商品');
    }

    /** @test */
    public function マイリスト_未認証の場合は何も表示されない()
    {
        $item = Item::factory()->create(['name' => 'おすすめ商品']);

        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('おすすめ商品');
    }
}
