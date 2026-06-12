<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    // 4.商品一覧取得

    /** @test */
    public function 商品一覧_全商品を取得できる()
    {
        $user = User::factory()->create();

        $condition = \App\Models\Condition::factory()->create();

        $item1 = Item::factory()->create([
            'name' => '商品A',
            'user_id' => $user->id,
            ]);
        $item2 = Item::factory()->create([
            'name' => '商品B',
            'user_id' => $user->id,
            ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /** @test */
    public function 商品一覧_購入済み商品は_Sold_と表示される()
    {
        $user = User::factory()->create();

        $condition = \App\Models\Condition::factory()->create(['name' => '新品']);

        $soldItem = Item::factory()->create([
        'name' => '売り切れ品',
        'status' => 'sold',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    /** @test */
    public function 商品一覧_自分が出品した商品は表示されない()
    {
        $me = User::factory()->create();
        $myCententItem = Item::factory()->create([
            'user_id' => $me->id,
            'name' => '私の出品物',
            ]);
        $otherItem = Item::factory()->create(['name' => '他人の出品物']);

        $response = $this->actingAs($me)->get('/');

        $response->assertDontSee('私の出品物');
        $response->assertSee('他人の出品物');
    }
}
