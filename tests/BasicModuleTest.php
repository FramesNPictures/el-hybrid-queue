<?php

namespace FNP\HybridQueue\Tests;

use Illuminate\Support\Facades\Schema;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function package_boots_and_config_is_merged(): void
    {
        $this->assertNotEmpty(config('queue.hybrid'));
    }

    /** @test */
    public function migrations_are_loadable_and_tables_can_be_created(): void
    {
        // Run the package migration
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate')->run();

        $this->assertTrue(Schema::hasTable('el_hybrid_queue_jobs'));
    }
}
