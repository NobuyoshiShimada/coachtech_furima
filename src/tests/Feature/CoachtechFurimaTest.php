<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class CoachtechFurimaTest extends TestCase
{
    use RefreshDatabase;

    // 1.会員登録機能

    /** @test */
    public function 会員登録_名前が入力されていない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function 会員登録_メールアドレスが入力されていない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function 会員登録_パスワードが入力されていない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function 会員登録_パスワードが7文字以下の場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function 会員登録_パスワードが確認用パスワードと一致しない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function 会員登録_全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

    }

    // 2.ログイン機能

    /** @test */
    public function ログイン_メールアドレスが入力されていない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function ログイン_パスワードが入力されていない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function ログイン_入力情報が間違っている場合_バリデーションメッセージが表示される()
    {
        $user = User::factory()->create([
            'email' => 'correct@example.com',
            'password' => bcrypt('correct_pass'),
        ]);

        $response = $this->post('/login', [
            'email' => 'correct@example.com',
            'password' => 'wrong_pass',
        ]);

        $response->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function ログイン_正しい情報が入力された場合_ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    // ログアウト機能

    /** @test */
    public function ログアウトができる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    // 商品一覧取得

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

    // マイリスト一覧取得

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

    // 6.商品詳細情報

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

