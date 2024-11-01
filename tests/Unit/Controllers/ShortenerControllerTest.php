<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ShortenedUrl;
use App\Repositories\ShortenedUrlRepositoryInterface;
use Mockery;

class ShortenerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $shortenedUrlRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->shortenedUrlRepository = Mockery::mock(ShortenedUrlRepositoryInterface::class);
        $this->app->instance(ShortenedUrlRepositoryInterface::class, $this->shortenedUrlRepository);
    }

    public function testCanRedirectToOriginalUrl()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $shortenedUrl = ShortenedUrl::factory()->create([
            'user_id' => $user->id,
            'shortened' => 'abc123',
            'url' => 'https://example.com'
        ]);

        $this->shortenedUrlRepository
            ->shouldReceive('findByShortened')
            ->with('abc123')
            ->andReturn($shortenedUrl);

        $response = $this->get(route('go', ['short' => 'abc123']));

        $response->assertRedirect('https://example.com');
    }

    public function testCannotRedirectToOriginalUrlIfNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->shortenedUrlRepository
            ->shouldReceive('findByShortened')
            ->with('invalidcode')
            ->andReturn(null);

        $response = $this->get(route('go', ['short' => 'invalidcode']));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'URL not found.');
    }

    public function testCanStoreAShortenedUrl()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $this->shortenedUrlRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn(new ShortenedUrl());

        $response = $this->post(route('shortener.store'), [
            'url' => 'https://example.com'
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'URL shortened successfully!');
    }

    public function testCannotStoreAShortenedUrlIfValidationFails()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('shortener.store'), [
            'url' => ''
        ]);

        $response->assertSessionHasErrors('url');
    }

    public function testCannotStoreAShortenedUrlOnException()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->shortenedUrlRepository
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->post(route('shortener.store'), [
            'url' => 'https://example.com'
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Failed to shorten URL. Please try again.');
    }

    public function testCanListShortenedUrls()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $this->shortenedUrlRepository
            ->shouldReceive('findByUserId')
            ->with($user->id, 5, 1)
            ->andReturn(collect([]));

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function testCanDeleteAShortenedUrl()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $shortenedUrl = ShortenedUrl::factory()->create(['user_id' => $user->id]);

        $this->shortenedUrlRepository
            ->shouldReceive('delete')
            ->with($shortenedUrl->id, $user->id)
            ->andReturn(true);

        $response = $this->delete(route('shortener.delete', ['id' => $shortenedUrl->id]));

        $response->assertJson(['message' => 'URL deleted successfully.']);
    }

    public function testCannotDeleteAShortenedUrlIfNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->shortenedUrlRepository
            ->shouldReceive('delete')
            ->with(999, $user->id)
            ->andReturn(false);

        $response = $this->delete(route('shortener.delete', ['id' => 999]));

        $response->assertJson(['message' => 'URL not found or you do not have permission to delete this URL.'], 404);
    }
}
