<?php

namespace Meveto\Client\Exceptions\Enum;

use Meveto\Client\Exceptions\MevetoException;

/**
 * Class InvalidEnumValueException.
 */
class InvalidEnumValueException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'Provided value for enum is not valid.',
    ];
}
