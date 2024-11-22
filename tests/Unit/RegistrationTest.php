<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
  use RefreshDatabase;

  public function test_new_users_can_register()
  {
    $response = $this->post('/register', [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated(); // تأكد من تسجيل الدخول بعد التسجيل
    $response->assertRedirect('/dashboard');
  }
}
