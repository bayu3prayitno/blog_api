<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    private function generateJWTToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    public function test_can_get_posts()
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200);
    }

    public function test_can_create_post()
    {
        $user = User::factory()->create();
        $token = $this->generateJWTToken($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/posts', [
            'title' => 'Judul Test',
            'content' => 'Isi konten test',
            'status' => 'draft',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Judul Test',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Judul Test',
        ]);
    }

    public function test_can_show_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/v1/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $post->id,
                'title' => $post->title,
            ]);
    }

    public function test_can_update_post()
    {
        $user = User::factory()->create();
        $token = $this->generateJWTToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/v1/posts/' . $post->id, [
            'title' => 'Judul Update',
            'content' => 'Konten sudah diupdate',
            'status' => 'published',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Judul Update',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Judul Update',
        ]);
    }

    public function test_can_delete_post()
    {
        $user = User::factory()->create();
        $token = $this->generateJWTToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/posts/' . $post->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_create_post_validation_error()
    {
        $user = User::factory()->create();
        $token = $this->generateJWTToken($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/posts', [
            'title' => '',
            'content' => '',
            'status' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_post_not_found()
    {
        $response = $this->getJson('/api/v1/posts/999999');

        $response->assertStatus(404);
    }
}
