<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    // 12.配送先変更

    /** @test */
    public function 配送先変更_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'postcode' => '123-4567',
            'address' => '富山県富山市',
            'building' => 'マルート',
        ]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postcode' => '765-4321',
            'address' => '石川県金沢市',
            'building' => 'フォーラス',
        ]);

        $response->assertRedirect("/purchase/{$item->id}");

        $purchaseResponse = $this->actingAs($user)->get("/purchase/{$item->id}");

        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertSee('765-4321');
        $purchaseResponse->assertSee('石川県金沢市');
        $purchaseResponse->assertSee('フォーラス');
    }

    /** @test */
    public function 配送先変更_購入した商品に送付先住所が紐づいて登録される()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'postcode' => '123-4567',
            'address' => '富山県富山市',
            'building' => 'マルート',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postcode' => '765-4321',
            'address' => '石川県金沢市',
            'building' => 'フォーラス',
        ]);

        $this->actingAs($user)->get("purchase/success/{$item->id}");

        $this->assertDatabaseHas('orders',[
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertDatabaseHas('profiles',[
            'user_id' => $user->id,
            'postcode' => '765-4321',
            'address' => '石川県金沢市',
            'building' => 'フォーラス'
        ]);
    }
}
