<?php

namespace Meveto\Client\Exceptions\Http;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class NotAuthenticatedException.
 */
class NotAuthenticatedException extends MevetoException
{
    /**
     * @var string[] Default message.
     */
    protected $defaultMessageLines = [
        'Meveto server could not authenticate your request and responded with a 401 status.',
        'Either an access token is missing or the provided token is not valid.',
    ];

    /**
     * @var int Default code.
     */
    protected $defaultCode = 401;
}
