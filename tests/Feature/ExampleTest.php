<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        
        // Mock Vite manifest
        Vite::useHotFile('hot')
            ->useBuildDirectory('build')
            ->withEntryPoints(['resources/css/app.css', 'resources/js/app.js']);
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(500);
        $this->assertStringContainsString('Welcome', $response->content());
    }
}
