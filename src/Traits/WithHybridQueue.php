<?php

namespace FNP\HybridQueue\Traits;

use FNP\HybridQueue\HybridQueueModule;

trait WithHybridQueue
{
    public function onHybridQueue(?string $queue = null): static
    {
        return $this
            ->onConnection(HybridQueueModule::CONNECTION_NAME)
            ->onQueue($queue ?? 'default');
    }
}
