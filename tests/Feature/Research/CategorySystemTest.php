<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use App\Services\ResearchCategoryService;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategorySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_seeder_populates_academic_categories(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseHas('research_categories', [
            'slug' => 'artificial-intelligence',
            'name' => 'Artificial Intelligence',
        ]);
        $this->assertDatabaseHas('research_categories', [
            'slug' => 'cyber-security',
            'name' => 'Cyber Security',
        ]);
    }

    public function test_public_categories_index_page_loads(): void
    {
        ResearchCategory::create([
            'name' => 'Data Science',
            'slug' => 'data-science',
            'description' => 'Big data analytics and data mining.',
        ]);

        $response = $this->get('/categories');

        $response->assertStatus(200);
        $response->assertSee('Data Science');
    }

    public function test_public_category_show_page_displays_related_published_research(): void
    {
        $user = User::factory()->create();
        $category = ResearchCategory::create([
            'name' => 'Medicine',
            'slug' => 'medicine',
        ]);

        $research = Research::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Immunology Advances Study',
            'slug' => 'immunology-advances-study',
            'abstract' => 'Clinical study on immunology breakthrough treatments.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'download_permission' => 'free',
            'status' => 'published',
        ]);

        $response = $this->get('/categories/medicine');

        $response->assertStatus(200);
        $response->assertSee('Immunology Advances Study');
    }

    public function test_corrupted_cache_is_recovered(): void
    {
        ResearchCategory::create(['name' => 'Valid Category', 'slug' => 'valid']);
        $service = app(ResearchCategoryService::class);

        Cache::put(ResearchCategoryService::CACHE_KEY, 'corrupted_string');

        $categories = $service->getAllCached();
        $this->assertInstanceOf(Collection::class, $categories);
        $this->assertNotEmpty($categories);

        $incomplete = new \__PHP_Incomplete_Class;
        $collection = collect([$incomplete]);
        Cache::put(ResearchCategoryService::CACHE_KEY, $collection);

        $categories2 = $service->getAllCached();
        $this->assertInstanceOf(Collection::class, $categories2);
        $this->assertFalse($categories2->contains(fn ($c) => $c instanceof \__PHP_Incomplete_Class));
    }

    public function test_category_updates_clear_cache(): void
    {
        $category = ResearchCategory::create(['name' => 'Original Name', 'slug' => 'original']);
        $service = app(ResearchCategoryService::class);

        $service->getAllCached();
        $this->assertTrue(Cache::has(ResearchCategoryService::CACHE_KEY));

        $category->update(['name' => 'New Name']);
        $this->assertFalse(Cache::has(ResearchCategoryService::CACHE_KEY));
    }
}
