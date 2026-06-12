<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    // 7.商品詳細情報

    /** @test */
    public function 商品詳細情報_必要な情報が表示される()
    {
        $user = User::factory()->create(['name' => 'コメント太郎']);
        $category = Category::create(['name' => 'メンズ']);
        $condition = Condition::create(['name' => '新品、未使用']);

        $item = Item::factory()->create([
            'name' => '詳細サンプル商品',
            'brand' => 'サンプルブランド',
            'price' => 10000,
            'description' => 'これはサンプル商品の説明です。',
            'condition_id' => $condition->id,
            ]);

        $item->categories()->attach($category->id);

        $item->likes()->create(['user_id' => $user->id]);
        $item->comments()->create([
            'user_id' => $user->id,
            'content' => 'この商品を教えてください。'
        ]);

        $response = $this->get('/items/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('詳細サンプル商品');
        $response->assertSee('サンプルブランド');
        $response->assertSee(10,000);
        $response->assertSee('これはサンプル商品の説明です。');
        $response->assertSee('メンズ');
        $response->assertSee('新品、未使用');
        $response->assertSee('1');
        $response->assertSee('コメント太郎');
        $response->assertSee('この商品を教えてください。');
    }

    /** @test */
    public function 商品詳細情報_複数選択されたカテゴリが表示されているか()
    {
        $category1 = Category::create(['name' => 'メンズ']);
        $category2 = Category::create(['name' => '靴']);

        $item = Item::factory()->create();

        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get('/items/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('メンズ');
        $response->assertSee('靴');
    }
}
