<?php

namespace Meveto\Client\Exceptions\InvalidConfig;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class StateNotSetException.
 */
class StateNotSetException extends MevetoException
{
    /**
     * @var string[] Default message.
     */
    protected $defaultMessageLines = [
        'Current application request state is not set.'
    ];
}
