<?php

return [
    'hybrid' => [
        // Table to hold the queue
        // -----------------------
        'table' => 'app_hybrid_queue',

        'timing' => [
            // How often to check for the jobs (in minutes)
            // --------------------------------------------
            'every' => 5,

            // How far in advance to pull jobs from the queue (in minutes)
            // -----------------------------------------------------------
            'advance' => 15,
        ],
    ],
];
