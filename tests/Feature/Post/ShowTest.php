<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
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

    public function test_return_post_by_id()
    {
        $post = Post::factory(1)->create()->first();
        $response = $this->get('/api/posts/'.$post->id);
        
        $response->assertStatus(200);
        //точное соответствие
        $response->assertExactJson(
            [
                'data'=>[
                    'id' => $post->id,
                    'title' => $post->title,
                    'body' => $post->body,
                    'published' => $post->published,
                    'commentCount' => 0,
                    'preview' => null,
                ]
            ]
        );
        // dd($response->getContent());
    }
}
