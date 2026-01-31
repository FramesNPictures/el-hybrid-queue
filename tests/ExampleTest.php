<?php

namespace ElHybridQueue\Tests;

use Illuminate\Support\Facades\Schema;

class ExampleTest extends TestCase
{
    /** @test */
    public function package_boots_and_config_is_merged()
    {
        $this->assertNotEmpty(config('queue.hybrid'));
    }

    /** @test */
    public function migrations_are_loadable_and_tables_can_be_created()
    {
        // Run the package migration
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate')->run();

        $this->assertTrue(Schema::hasTable('el_hybrid_queue_jobs'));
    }
}
