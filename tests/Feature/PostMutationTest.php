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
  createPost(input: { title: "Hello", content: "World", author: { connect: $authorId } }) {
    title
    author {
      id
    }
  }
}
        ', ['authorId' => $author->id])->assertExactJson([
            'data' => [
                'createPost' => [
                    'title' => 'Hello',
                    'author' => ['id' => (string) $author->id],
                ],
            ],
        ]);
    }

    public function testUpdatePost(): void
    {
        $post = Post::factory()->create();
        $newAuthor = User::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
mutation ($id: ID!, $authorId: ID!) {
  updatePost(input: { id: $id, title: "Changed", author: { connect: $authorId } }) {
    title
    author {
      id
    }
  }
}
        ', ['id' => $post->id, 'authorId' => $newAuthor->id])->assertExactJson([
            'data' => [
                'updatePost' => [
                    'title' => 'Changed',
                    'author' => ['id' => (string) $newAuthor->id],
                ],
            ],
        ]);
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
        ', ['id' => $post->id])->assertExactJson([
            'data' => ['deletePost' => ['id' => (string) $post->id]],
        ]);

        $this->assertModelMissing($post);
    }
}
