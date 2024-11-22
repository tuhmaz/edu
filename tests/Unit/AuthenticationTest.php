<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_users_can_authenticate_using_the_login_screen()
  {
    $user = \App\Models\User::factory()->create([
      'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'password123',
    ]);

    $this->assertAuthenticated(); // تأكد من تسجيل الدخول
    $response->assertRedirect('/dashboard');
  }
}
