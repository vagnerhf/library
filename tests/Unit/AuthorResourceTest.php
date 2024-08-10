<?php

namespace Tests\Unit;

use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_resource_has_correct_structure()
    {
        $author = Author::factory()->create();
        $resource = new AuthorResource($author);
        $arrayResource = $resource->toArray(request());

        $this->assertEquals([
            'key' => $author->key,
            'name' => $author->name,
            'birth_date' => $author->birth_date,
            'created_at' => $author->created_at->toDateTimeString(),
            'updated_at' => $author->updated_at->toDateTimeString(),
        ], $arrayResource);
    }
}
