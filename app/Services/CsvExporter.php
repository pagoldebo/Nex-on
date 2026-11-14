<?php

namespace App\Services;

class CsvExporter
{
    /**
     * Sanitize an array of fields against CSV / Spreadsheet formula injection (CWE-1236).
     * Prefixes cells starting with '=', '+', '-', '@', '\t', '\r' with a single quote.
     */
    public static function sanitizeRow(array $fields): array
    {
        foreach ($fields as $key => $value) {
            if (is_string($value)) {
                $firstChar = substr($value, 0, 1);
                if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
                    $fields[$key] = "'" . $value;
                }
            }
        }
        return $fields;
    }

    /**
     * Write a sanitized row to a CSV stream handle.
     */
    public static function writeRow($handle, array $fields): int|false
    {
        return fputcsv($handle, static::sanitizeRow($fields));
    }
}
