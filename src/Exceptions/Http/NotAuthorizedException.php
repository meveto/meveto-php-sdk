<?php

namespace Meveto\Client\Exceptions\Http;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class NotAuthorizedException.
 */
class NotAuthorizedException extends MevetoException
{
    /**
     * @var string[] Default message.
     */
    protected $defaultMessageLines = [
        'The specified access token is not authorized to access the requested information'
    ];

    /**
     * @var int Default code.
     */
    protected $defaultCode = 403;
}
