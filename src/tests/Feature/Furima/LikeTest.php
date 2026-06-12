<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    // 8.いいね

    /** @test */
    public function いいね_いいねアイコンを押下することによって_いいねした商品として登録することができる_追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->postJson("/items/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson([
            'isLiked' => true,
            'likesCount' => 1
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        $detailResponse = $this->actingAs($user)->get("/items/" . $item->id);
        $detailResponse->assertSee('action-buttons__btn--active');
    }

    /** @test */
    public function いいね_再度いいねアイコンを押下することによって_いいねを解除することができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item->likes()->create(['user_id' => $user->id]);

        $this->assertEquals(1, $item->likes()->count());

        $response = $this->actingAs($user)->postJson("/items/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson([
            'isLiked' => false,
            'likesCount' => 0
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        $detailResponse = $this->actingAs($user)->get("/item" . $item->id);
        $detailResponse->assertDontSee('action-buttons__btn--active');
    }
}
