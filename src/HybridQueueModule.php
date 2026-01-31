<?php

namespace ElHybridQueue;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigMerge;
use Fnp\ElModule\Features\ModuleMigrations;
use Illuminate\Support\ServiceProvider;

class HybridQueueModule extends ElModule
{
    use ModuleConfigMerge;
    use ModuleMigrations;

    public function defineConfigMergeFiles(): array
    {
        return [
            'queue' => __DIR__ . '/../config/queue.php'
        ];
    }

    public function defineMigrationFolders(): array
    {
        return [
            __DIR__ . '/../database/migrations'
        ];
    }
}
