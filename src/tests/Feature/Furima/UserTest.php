<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // 13.ユーザー情報取得

    /** @test */
    public function ユーザー情報取得_必要な情報が取得できる()
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        $user->profile()->create([
            'postcode' => '111-1111',
            'address' => '新潟県新潟市',
            'building' => '朱鷺メッセ',
            'image_url' => 'profiles/test_avatar.jpeg'
        ]);

        $mySellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した本',
        ]);
        $myBuyItem = Item::factory()->create(['name' => '購入した時計']);
        $myBuyItem->order()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('test_avatar.jpeg');
        $response->assertSee('出品した本');

        $buyTabResponse = $this->actingAs($user)->get('/mypage?tab=buy');
        $buyTabResponse->assertSee('購入した時計');
    }

    // ユーザー情報変更

    /** @test */
    public function ユーザー情報取得_変更項目が初期値として過去設定されていること()
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        $user->profile()->create([
            'postcode' => '111-1111',
            'address' => '新潟県新潟市',
            'building' => '朱鷺メッセ',
            'image_url' => 'profiles/test_avatar.jpeg'
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('value="テスト太郎"', false);
        $response->assertSee('value="111-1111"', false);
        $response->assertSee('value="新潟県新潟市"', false);
        $response->assertSee('value="朱鷺メッセ"', false);
        $response->assertSee('test_avatar.jpeg');
    }
}
