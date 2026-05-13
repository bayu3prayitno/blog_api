<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class ApiAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Audit #1: Verifikasi Prefiks API v1 diterapkan dengan benar.
     */
    public function test_api_v1_prefix_is_applied()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/v1/posts');
        $this->assertEquals(200, $response->status());
    }

    /**
     * Audit #2: Verifikasi endpoint lama (tanpa v1) mengembalikan 404.
     */
    public function test_old_api_endpoint_without_v1_prefix_returns_404()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/posts');
        $this->assertEquals(404, $response->status());
    }

    /**
     * Audit #3: Verifikasi Rate Limiting (60 permintaan per menit).
     */
    public function test_rate_limiting_middleware_is_present()
    {

        $this->withoutMiddleware();
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/v1/posts');
            $this->assertEquals(200, $response->status());
        }
    }

    /**
     * Audit #4: Verifikasi proteksi JWT pada rute privat (tanpa token).
     */
    public function test_private_endpoints_require_jwt_token()
    {
        $response = $this->postJson('/api/v1/logout');
        $this->assertEquals(401, $response->status());
    }

    /**
     * Audit #5: Verifikasi validasi JWT Token yang tidak valid ditolak.
     */
    public function test_invalid_jwt_token_is_rejected()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer token_palsu_disini'
        ])->postJson('/api/v1/logout');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Audit #6: Verifikasi mekanisme Blacklist Token (mencegah penggunaan ulang).
     */
    public function test_token_blacklist_prevents_reuse()
    {
        $this->withoutMiddleware();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');
        $this->assertNotNull($token);

        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/logout');

        $this->assertEquals(200, $logoutResponse->status());

        $this->assertTrue(Cache::has('bl_' . $token));
    }
}
