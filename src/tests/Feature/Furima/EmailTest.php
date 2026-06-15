<?php

namespace Tests\Feature\furima;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\support\facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メール認証_会員登録後認証メールが送信される()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'test_name',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

            /** @test */
    public function メール認証_メール認証誘導画面で認証はこちらからボタンを押下するとメール認証サイトに遷移する_メール認証サイトのメール認証を完了するとプロフィール設定画面に遷移する()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
        ->withSession(['auth.verify.user_id' => $user->id])
        ->get('/email/verify');

        $response->assertStatus(200);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/mypage/profile');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
