<?php

namespace Tests\Feature\Like;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(
            [
                'accept' => 'application/json',
            ]
        );
    }

    public function test_post_can_not_lake_by_unauthorised_user()
    {
        $postId = mt_rand(1, 10);

        $response = $this->post('/api/postLike/'.$postId);

        $response->assertStatus(401);
    }

    public function test_post_can_lake_by_authorised_user()
    {
        $post = Post::factory(1)->create()->first();

        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $response = $this->actingAs($user)->post('/api/postLike/'.$post->id);

        $response->assertStatus(200);
        $this->assertDatabaseCount('likes', 1);
        // точное соответствие
        $response->assertExactJson(
            [
                'is_liked' => true,
                'likes_count' => 1,
            ]
        );

        $response = $this->actingAs($user)->post('/api/postLike/'.$post->id);

        $this->assertDatabaseCount('likes', 0);
        // точное соответствие
        $response->assertExactJson(
            [
                'is_liked' => false,
                'likes_count' => 0,
            ]
        );

        // dd($response->getContent());
    }
}
