<?php

namespace Tests\Feature\Furima;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminateupport\Facadestorage;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

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

    // 3.ログアウト機能

    /** @test */
    public function ログアウトができる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
