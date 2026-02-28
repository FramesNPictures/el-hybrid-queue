<?php

namespace Fnp\ElStart\Tests\Feature;

use Fnp\ElStart\Tests\TestCase;
use Schema;

class ExampleTest extends TestCase
{
    public function test_it_can_load_the_module(): void
    {
        $this->assertTrue(true);
    }

    public function test_it_has_migrations(): void
    {
        $this->assertTrue(Schema::hasTable('app_users'));
    }

    public function test_it_overrides_config(): void
    {
        $this->assertEquals('app_jobs', config('queue.connections.database.table'));
        $this->assertEquals('app_migrations', config('database.migrations.table'));
    }
}
