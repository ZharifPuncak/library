<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MediaSeeder extends Seeder
{
    /**
     * Default categories used if the `categories` table is empty.
     */
    protected array $defaultCategories = [
        'Annual Reports',
        'Magazines',
        'Newspapers',
        'Brochures',
        'Company Profiles',
        'Country Reports',
    ];

    /**
     * Default tags seeded when the `tags` table is empty.
     */
    protected array $defaultTags = [
        'corporate', 'archive', 'historical', 'public', 'internal',
        'training', 'reference', 'event', 'feature', 'compliance',
    ];

    /**
     * Sample items per asset type. Files don't need to exist on disk;
     * the rows let you verify category filters and listings work.
     */
    protected array $samples = [
        'photo' => [
            ['title' => 'Headquarters Exterior',        'file_path' => 'seed/photos/hq-exterior.jpg'],
            ['title' => 'Library Reading Room',         'file_path' => 'seed/photos/reading-room.jpg'],
            ['title' => 'Archive Shelves',              'file_path' => 'seed/photos/archive-shelves.jpg'],
        ],
        'video' => [
            ['title' => 'Corporate Overview',           'file_path' => 'seed/videos/corporate-overview.mp4', 'thumbnail_path' => 'seed/videos/corporate-overview.jpg'],
            ['title' => 'Library Tour Walkthrough',     'file_path' => 'seed/videos/library-tour.mp4',       'thumbnail_path' => 'seed/videos/library-tour.jpg'],
        ],
        'ebook' => [
            ['title' => 'Reference Handbook',           'file_path' => 'seed/ebooks/reference-handbook.pdf'],
            ['title' => 'Annual Compendium',            'file_path' => 'seed/ebooks/annual-compendium.pdf'],
        ],
    ];

    public function run(): void
    {
        if (Category::count() === 0) {
            foreach ($this->defaultCategories as $name) {
                Category::firstOrCreate(['name' => $name]);
            }
            $this->command->info('Seeded ' . count($this->defaultCategories) . ' default categories.');
        }

        if (Tag::count() === 0) {
            foreach ($this->defaultTags as $name) {
                Tag::firstOrCreate(['name' => $name]);
            }
            $this->command->info('Seeded ' . count($this->defaultTags) . ' default tags.');
        }

        $categories = Category::all();
        $tagIds     = Tag::pluck('id')->all();
        $created    = 0;

        foreach ($categories as $category) {
            foreach ($this->samples as $type => $items) {
                foreach ($items as $item) {
                    $media = Media::firstOrCreate(
                        [
                            'type'  => $type,
                            'title' => $item['title'] . ' — ' . $category->name,
                        ],
                        [
                            'file_path'      => $item['file_path'],
                            'thumbnail_path' => $item['thumbnail_path'] ?? null,
                            'date'           => Carbon::now()->subDays(random_int(0, 365)),
                        ]
                    );

                    $media->categories()->syncWithoutDetaching([$category->id]);

                    // Attach 2-3 random tags per media row.
                    $picked = collect($tagIds)->shuffle()->take(random_int(2, 3))->all();
                    $media->tags()->syncWithoutDetaching($picked);

                    $created++;
                }
            }
        }

        $this->command->info("Linked {$created} media rows across {$categories->count()} categories and " . count($tagIds) . " tags.");
    }
}
