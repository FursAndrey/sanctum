<?php

namespace Tests\Feature;

use Tests\TestCase;

class CSFRTest extends TestCase
{
    public function test_the_application_returns_csrf_cookie(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response
            ->assertStatus(204)
            ->assertCookieNotExpired('XSRF-TOKEN')
            ->assertCookieNotExpired('laravel_session');
    }
}
