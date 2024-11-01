<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ShortenedUrl;
use App\Repositories\ShortenedUrlRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShortenedUrlRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ShortenedUrlRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ShortenedUrlRepository();
    }

    public function testCanCreateAShortenedUrl()
    {
        $user = User::factory()->create();
        $data = [
            'url' => 'https://example.com',
            'shortened' => 'abc123',
            'user_id' => $user->id,
        ];

        $shortenedUrl = $this->repository->create($data);

        $this->assertInstanceOf(ShortenedUrl::class, $shortenedUrl);
        $this->assertDatabaseHas('shortened_urls', $data);
    }

    public function testCanFindByUserId()
    {
        $user = User::factory()->create();
        ShortenedUrl::factory()->create(['user_id' => $user->id]);
        ShortenedUrl::factory()->create(['user_id' => $user->id]);

        $urls = $this->repository->findByUserId($user->id, 10, 1);

        $this->assertCount(2, $urls);
    }

    public function testCanDeleteAShortenedUrl()
    {
        $user = User::factory()->create();
        $shortenedUrl = ShortenedUrl::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->delete($shortenedUrl->id, $user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('shortened_urls', ['id' => $shortenedUrl->id]);
    }

    public function testCannotDeleteAShortenedUrlIfNotFound()
    {
        $user = User::factory()->create();

        $result = $this->repository->delete(999, $user->id);

        $this->assertFalse($result);
    }

    public function testCanFindByShortenedCode()
    {
        $user = User::factory()->create();
        $shortenedUrl = ShortenedUrl::factory()->create(['shortened' => 'abc123', 'user_id' => $user->id]);

        $foundUrl = $this->repository->findByShortened('abc123');

        $this->assertEquals($shortenedUrl->id, $foundUrl->id);
    }

    public function testCannotFindByShortenedCodeIfNotFound()
    {
        $foundUrl = $this->repository->findByShortened('nonexistent');
        $this->assertNull($foundUrl);
    }
}
