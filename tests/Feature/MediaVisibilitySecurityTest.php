<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaVisibilitySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_media_surfaces_hide_unpublished_media(): void
    {
        $user = $this->user();
        $published = $this->media('Visible Published', 'published');
        $draft = $this->media('Hidden Draft', 'draft');
        $archived = $this->media('Hidden Archived', 'archived');

        $this->actingAs($user)
            ->get(route('media.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title)
            ->assertDontSee($archived->title);

        $this->withSession(['recently_viewed' => [$draft->id, $published->id]])
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee($draft->title)
            ->assertDontSee($archived->title);
    }

    public function test_non_admin_direct_access_to_unpublished_media_returns_404(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get(route('media.show', $this->media('Hidden Draft', 'draft')))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('media.show', $this->media('Visible Published', 'published')))
            ->assertOk()
            ->assertSee('Visible Published');
    }

    public function test_admin_can_view_unpublished_media(): void
    {
        $admin = $this->user('admin');
        $draft = $this->media('Admin Draft', 'draft');

        $this->actingAs($admin)
            ->get(route('media.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSee($draft->title);

        $this->actingAs($admin)
            ->get(route('media.show', $draft))
            ->assertOk()
            ->assertSee($draft->title);
    }

    public function test_collections_only_show_and_download_visible_media_for_non_admins(): void
    {
        Storage::fake('public');

        $user = $this->user();
        $collection = Collection::create(['name' => 'Security Collection']);
        $published = $this->media('Visible Collection Item', 'published', ['file_path' => 'media/visible.pdf']);
        $draft = $this->media('Hidden Collection Item', 'draft', ['file_path' => 'media/hidden.pdf']);

        Storage::disk('public')->put($published->file_path, 'visible');
        Storage::disk('public')->put($draft->file_path, 'hidden');

        $this->tagToCollection($published, $collection);
        $this->tagToCollection($draft, $collection);

        $this->actingAs($user)
            ->get(route('collections.show', $collection))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);

        $this->actingAs($user)
            ->get(route('collections.media.show', [$collection, $draft]))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('collections.download', $collection))
            ->assertDownload();
    }

    public function test_svg_photo_upload_is_rejected(): void
    {
        Storage::fake('public');

        $admin = $this->user('admin');
        $svg = UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $this->actingAs($admin)
            ->post(route('collections.store'), [
                'name'     => 'SVG Collection ' . uniqid(),
                'status'   => 'published',
                'files'    => [$svg],
            ])
            ->assertSessionHasErrors('files.0');
    }

    public function test_allowed_photo_upload_still_passes(): void
    {
        Storage::fake('public');

        $admin = $this->user('admin');
        $name  = 'Safe Photos ' . uniqid();

        $this->actingAs($admin)
            ->post(route('collections.store'), [
                'name'   => $name,
                'status' => 'published',
                'files'  => [UploadedFile::fake()->image('safe.jpg')],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('media', ['title' => 'safe', 'status' => 'published', 'type' => 'photo']);
        $this->assertDatabaseHas('collections', ['name' => $name]);
    }

    private function user(string $role = 'user'): User
    {
        return User::create([
            'name' => ucfirst($role),
            'username' => $role . uniqid(),
            'email' => $role . uniqid() . '@example.com',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function media(string $title, string $status, array $overrides = []): Media
    {
        return Media::create(array_merge([
            'title' => $title,
            'type' => 'photo',
            'status' => $status,
            'date' => now(),
        ], $overrides));
    }

    private function tagToCollection(Media $media, Collection $collection): void
    {
        $media->details()->create([
            'key' => 'collection',
            'value' => $collection->name,
        ]);
    }
}
