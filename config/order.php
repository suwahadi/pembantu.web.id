<?php

/**
 * Configuration untuk Order, Dispute, dan Escrow behavior
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Dispute Window (hours)
    |--------------------------------------------------------------------------
    |
    | Jangka waktu dalam jam dimana visitor/agency dapat membuka dispute
    | setelah order completed. Setelah window ini lewat tanpa dispute,
    | escrow akan auto-release via job scheduler.
    |
    | Default: 24 jam
    */
    'dispute_window_hours' => env('ORDER_DISPUTE_WINDOW_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Auto Release Job Schedule
    |--------------------------------------------------------------------------
    |
    | Schedule untuk ReleaseEscrowJob dalam format cron
    | Default: every 15 minutes
    */
    'release_escrow_schedule' => env('ORDER_RELEASE_ESCROW_SCHEDULE', '*/15 * * * *'),

    /*
    |--------------------------------------------------------------------------
    | Statuses enum
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'pending_payment',
        'paid_escrow',
        'in_progress',
        'completed',
        'disputed',
        'refund_pending',
        'payout_pending',
        'refunded',
        'partially_refunded',
        'released',
        'cancelled',
    ],
];
