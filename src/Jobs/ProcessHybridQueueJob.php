<?php

namespace FNP\HybridQueue\Jobs;

use FNP\HybridQueue\HybridQueueModule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class ProcessHybridQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Process hybrid queue
        // --------------------
        $availableFrom = time() - (HybridQueueModule::FETCH_BEHIND_MINUTES * 60);
        $availableUntil = time() + (HybridQueueModule::FETCH_AHEAD_MINUTES * 60);
        $numberOfAttempts = HybridQueueModule::FETCH_ATTEMPTS;
        $attemptCooldown = HybridQueueModule::FETCH_COOLDOWN;
        $failedJobsTable = config('queue.failed.table', 'app_jobs_failed');

        DB::table(HybridQueueModule::TABLE_NAME)
            ->where('available_at', '>=', $availableFrom)
            ->where('available_at', '<=', $availableUntil)
            ->whereNull('reserved_at')
            ->where('attempts', '<=', $numberOfAttempts)
            ->chunkById(100, function ($jobs) use (
                $failedJobsTable,
                $numberOfAttempts,
                $attemptCooldown
            ): void {
                foreach ($jobs as $job) {
                    // Mark job as reserved
                    // --------------------
                    DB::table(HybridQueueModule::TABLE_NAME)
                        ->where('id', $job->id)
                        ->increment('attempts', [
                            'reserved_at' => time(),
                        ]);

                    try {
                        // Unserialize the job
                        // -------------------
                        $payload = json_decode($job->payload, true);
                        $command = unserialize($payload['data']['command']);

                        // Push the job to the queue
                        // -------------------------
                        Queue::pushOn($job->queue, $command);

                        // Delete the job after successful push
                        // ------------------------------------
                        DB::table(HybridQueueModule::TABLE_NAME)
                            ->where('id', $job->id)
                            ->delete();
                    } catch (Throwable $e) {
                        // Check if attempts are exhausted
                        // -------------------------------
                        if ($job->attempts >= $numberOfAttempts) {

                            // Add to failed_jobs table
                            // ------------------------
                            DB::table($failedJobsTable)->insert([
                                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                                'connection' => HybridQueueModule::CONNECTION_NAME,
                                'queue' => $job->queue,
                                'payload' => $job->payload,
                                'exception' => (string) $e,
                                'failed_at' => now(),
                            ]);

                            // Remove from hybrid queue table
                            // ------------------------------
                            DB::table(HybridQueueModule::TABLE_NAME)
                                ->where('id', $job->id)
                                ->delete();
                        } else {

                            // Release job back to queue on failure
                            // ------------------------------------
                            DB::table(HybridQueueModule::TABLE_NAME)
                                ->where('id', $job->id)
                                ->update([
                                    'reserved_at' => null,
                                    'available_at' => time() + $attemptCooldown,
                                ]);
                        }

                        report($e);
                    }
                }
            });
    }
}
