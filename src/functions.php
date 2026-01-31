<?php

// Global helper functions for the el-hybrid-queue package.
// These are loaded by the package's service provider at register() time.

if (!function_exists('queueHybrid')) {
    /**
     * Returns a simple identifier for this package.
     * Kept minimal; primarily useful for smoke tests or debugging.
     */
    function queueHybrid(): void
    {
        // Do nothing;
    }
}
