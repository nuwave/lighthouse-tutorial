<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class PostMutationTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    public function testCreatePost(): void
    {
        $author = User::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
        mutation ($authorId: ID!) {
            createPost(author_id: $authorId, title: "Hello", content: "World") {
                title
                author {
                    id
                }
            }
        }
        ', ['authorId' => $author->id])->assertJson([
            'data' => [
                'createPost' => [
                    'title' => 'Hello',
                    'author' => ['id' => (string) $author->id],
                ],
            ],
        ]);

        $this->assertDatabaseHas('posts', ['title' => 'Hello', 'author_id' => $author->id]);
    }

    public function testUpdatePost(): void
    {
        $post = Post::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
        mutation ($id: ID!) {
            updatePost(id: $id, title: "Changed") {
                title
            }
        }
        ', ['id' => $post->id])->assertJson([
            'data' => ['updatePost' => ['title' => 'Changed']],
        ]);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Changed']);
    }

    public function testDeletePost(): void
    {
        $post = Post::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
        mutation ($id: ID!) {
            deletePost(id: $id) {
                id
            }
        }
        ', ['id' => $post->id])->assertJson([
            'data' => ['deletePost' => ['id' => (string) $post->id]],
        ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
