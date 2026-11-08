<?php

namespace App\Services;

class SqlSafetyService
{
    /**
     * Escape special characters in a string for use in SQL LIKE clauses.
     * Prevents wildcards (% and _) from triggering full table scans or unexpected matches.
     */
    public static function escapeLike(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char . $char, $char . '%', $char . '_'],
            $value
        );
    }
}

