<?php
/**
 * CommonHelper
 * 
 * This helper class provides common utility functions.
 * 
 * @package App\Helpers
 */

/**
 * Get the duration of a plan in days.
 *
 * @param string $planType The type of plan (day, week, month, year)
 * @return int The duration in days
 */
if (!function_exists('getPlanDuration')) {
    function getPlanDuration(string $planType): int
    {
        return match ($planType) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 1
        };
    }
}

