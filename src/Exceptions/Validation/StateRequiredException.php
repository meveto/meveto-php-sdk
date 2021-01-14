<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;

/**
 * Class StateRequiredException.
 */
class StateRequiredException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'Current application request state can not be empty.'
    ];
}
