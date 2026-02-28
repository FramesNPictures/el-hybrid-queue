<?php

namespace FNP\HybridQueue;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigMerge;
use Fnp\ElModule\Features\ModuleConfigOverride;
use Fnp\ElModule\Features\ModuleMigrations;
use Fnp\ElModule\Features\ModuleSchedule;
use FNP\HybridQueue\Jobs\ProcessHybridQueueJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bus\PendingDispatch;

class HybridQueueModule extends ElModule
{
    use ModuleConfigMerge;
    use ModuleConfigOverride;
    use ModuleMigrations;
    use ModuleSchedule;

    const TABLE_NAME = 'app_queue';

    const CONNECTION_NAME = 'el-hybrid-queue';

    const FETCH_AHEAD_MINUTES = 15;

    const FETCH_BEHIND_MINUTES = 60;

    const FETCH_ATTEMPTS = 3;

    const FETCH_COOLDOWN = 30;

    public function boot(): void
    {
        parent::boot();

        PendingDispatch::macro('onHybridQueue', function (?string $queue = null) {
            /** @var PendingDispatch $this */
            return $this
                ->onConnection(self::CONNECTION_NAME)
                ->onQueue($queue ?? 'default');
        });
    }

    public function defineConfigMergeFiles(): array
    {
        return [
            'queue' => __DIR__ . '/../config/queue.php',
        ];
    }

    public function defineMigrationFolders(): array
    {
        return [
            __DIR__ . '/../database/migrations',
        ];
    }

    public function defineConfigOverride(): array
    {
        return [
            'queue.connections.' . self::CONNECTION_NAME => [
                'driver' => 'database',
                'table' => self::TABLE_NAME,
            ],
        ];
    }

    public function defineSchedule(Schedule $scheduler): void
    {
        $scheduler->job(ProcessHybridQueueJob::class)
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();
    }
}
