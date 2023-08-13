<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(
            [
                'accept' => 'application/json',
            ]
        );
    }

    public function test_return_posts_if_not_exist_posts(): void
    {
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                "posts" => []
            ]
        );
        $response->assertJsonFragment(
            [
                "meta" => [
                    "current_page" => 1,
                    "from" => null,
                    "last_page" => 1,
                    "path" => config('app.url')."/api/posts",
                    "per_page" => 10,
                    "to" => null,
                    "total" => 0
                ]
            ]
        );
    }

    public function test_return_posts_if_exist_posts(): void
    {
        $posts = Post::factory(5)->create();
        $response = $this->get('/api/posts');
        
        $expectedJson = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'published' => $post->published,
            ];
        })->toArray();
        $expectedJson = array_reverse($expectedJson);
        
        $response->assertStatus(200);
        
        //описанные аттрибуты должны быть обязательно, кроме них могут быть и другие
        $response->assertJson(
            [
                'data' => [
                    "posts" => $expectedJson,
                    "meta" => [
                        "current_page" => 1,
                        "from" => 1,
                        "last_page" => 1,
                        "path" => config('app.url').'/api/posts',
                        "per_page" => 10,
                        "to" => 5,
                        "total" => 5
                    ]
                ]
            ]
        );
    }
}
