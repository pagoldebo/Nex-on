<?php

namespace Tests\Unit;

use App\Services\SqlSafetyService;
use Tests\TestCase;

class SqlSafetyServiceTest extends TestCase
{
    public function test_escapes_percentage_wildcard(): void
    {
        $input = '50% off';
        $escaped = SqlSafetyService::escapeLike($input);

        $this->assertEquals('50\\% off', $escaped);
    }

    public function test_escapes_underscore_wildcard(): void
    {
        $input = 'product_code_123';
        $escaped = SqlSafetyService::escapeLike($input);

        $this->assertEquals('product\\_code\\_123', $escaped);
    }

    public function test_escapes_backslashes_and_combined_wildcards(): void
    {
        $input = 'C:\\path\\_test%deal';
        $escaped = SqlSafetyService::escapeLike($input);

        $this->assertEquals('C:\\\\path\\\\\\_test\\%deal', $escaped);
    }

    public function test_leaves_clean_alphanumeric_strings_untouched(): void
    {
        $input = 'Regular Product Name 2026';
        $escaped = SqlSafetyService::escapeLike($input);

        $this->assertEquals('Regular Product Name 2026', $escaped);
    }
}

