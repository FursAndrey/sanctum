<?php

namespace Tests\Feature\Like;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentToggleTest extends TestCase
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

    public function test_comment_can_not_lake_by_unauthorised_user()
    {
        $commentId = mt_rand(1, 10);

        $response = $this->post('/api/commentLike/'.$commentId);

        $response->assertStatus(401);
    }

    public function test_comment_can_lake_by_authorised_user()
    {
        Post::factory(1)->create()->first();

        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'bot',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);
        
        $comments = Comment::factory(1)->create()->first();

        $response = $this->actingAs($user)->post('/api/commentLike/'.$comments->id);

        $response->assertStatus(200);
        $this->assertDatabaseCount('likes', 1);
        //точное соответствие
        $response->assertExactJson(
            [
                'is_liked' => true,
                'likes_count' => 1,
            ]
        );

        $response = $this->actingAs($user)->post('/api/commentLike/'.$comments->id);

        $this->assertDatabaseCount('likes', 0);
        //точное соответствие
        $response->assertExactJson(
            [
                'is_liked' => false,
                'likes_count' => 0,
            ]
        );

        // dd($response->getContent());
    }
}
