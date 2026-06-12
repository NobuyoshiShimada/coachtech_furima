<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    // 6.商品検索

    /** @test */
    public function 商品検索_商品名で部分一致検索ができる()
    {
        $item1 = Item::factory()->create(['name' => '高級な腕時計', 'status' => 'on_sale']);
        $item2 = Item::factory()->create(['name' => 'スニーカー', 'status' => 'on_sale']);

        $response = $this->get('/?keyword=腕時計');

        $response->assertSee('高級な腕時計');
        $response->assertDontSee('スニーカー');
    }

    /** @test */
    public function 商品検索_検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create(['name' => '限定スニーカー', 'status' => 'on_sale']);

        $item->likes()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/?keyword=スニーカー&tab=mylist');

        $response->assertSee('限定スニーカー');
        $response->assertSee('value="スニーカー"', false);
    }
}
