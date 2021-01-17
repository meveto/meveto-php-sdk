<?php

namespace Tests;

use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\TestCase;

/**
 * Class MevetoTestCase.
 */
abstract class MevetoTestCase extends TestCase
{
    /**
     * Overload string contains string.
     *
     * @param string $needle
     * @param string $haystack
     * @param string $message
     */
    public static function assertStringHasString($needle, $haystack, $message = '')
    {
        $constraint = new StringContains($needle, false);

        static::assertThat($haystack, $constraint, $message);
    }
}
