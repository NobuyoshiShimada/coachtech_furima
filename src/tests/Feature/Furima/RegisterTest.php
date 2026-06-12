<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class RegisterTest extends TestCase
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
}
