<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    // 15.出品情報登録

    /** @test */
    public function 出品情報取得_商品出品画面にて必要な情報が保存できること()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'メンズ']);
        $condition = Condition::create(['name' => 'やや傷や汚れあり']);

        app('migrator');
        $dummyImage = \Illuminate\Http\UploadedFile::fake()->create('exhibition_item.jpeg', 100);

        $response = $this->actingAs($user)->post('/sell', [
            'image' => $dummyImage,
            'categories' => [$category->id],
            'condition_id' => $condition->id,
            'name' => 'ビンテージジャケット',
            'brand' => '古着シャネル',
            'description' => '状態の良いレトロなジャケットです。',
            'price' => 35000,
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'ビンテージジャケット',
            'brand' => '古着シャネル',
            'description' => '状態の良いレトロなジャケットです。',
            'price' => 35000,
            'status' => 'on_sale'
        ]);

        $item = Item::where('name', 'ビンテージジャケット')->first();
        $this->assertTrue($item->categories->contains($category->id));
    }
}
