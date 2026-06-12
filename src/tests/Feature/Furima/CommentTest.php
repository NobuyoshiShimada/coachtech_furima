<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // 9.コメント送信

    /** @test */
    public function コメント送信_ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item->likes()->create(['user_id' => $user->id]);

        $this->assertEquals(0, $item->fresh()->comments()->count());

        $response = $this->actingAs($user)->post("/items/{$item->id}/comment", [
            'content' => 'テスト用のコメント内容です。'
        ]);

        $response->assertRedirect("/items/{$item->id}");

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テスト用のコメント内容です。',
        ]);

        $this->assertEquals(1, $item->fresh()->comments()->count());
    }

    /** @test */
    public function コメント送信_ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/items/{$item->id}/comment", [
            'content' => 'ログイン前のコメントです。'
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'content' => 'ログイン前のコメントです。',
        ]);
    }

    /** @test */
    public function コメント送信_コメントが入力されていない場合_バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/items/{$item->id}/comment", [
            'content' => ''
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function コメント送信_コメントが255文字以上の場合_バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post("/items/{$item->id}/comment", [
            'content' => $longComment
        ]);

        $response->assertSessionHasErrors(['content']);
    }
}
