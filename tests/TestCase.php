<?php

namespace ElHybridQueue\Tests;

use ElHybridQueue\HybridQueueModule;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            HybridQueueModule::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Use in-memory sqlite for tests
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Ensure package config is loaded
        $app['config']->set('el-hybrid-queue.enabled', true);
    }
}
