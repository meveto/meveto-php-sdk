<?php

namespace Meveto\Client\Exceptions\InvalidConfig;

use Meveto\Client\Exceptions\MevetoException;

/**
 * Class ConfigNotSetException.
 */
class ConfigNotSetException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'Your Meveto client configuration is not set.',
    ];
}
