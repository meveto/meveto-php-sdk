<?php

namespace Meveto\Client\Exceptions\InvalidClient;

use Meveto\Client\Exceptions\MevetoException;

/**
 * Class ClientNotFoundException.
 */
class ClientNotFoundException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'Your Meveto client credentials are incorrect.',
        'Check your client ID, secret and redirect URL.',
        'Redirect URL must be exactly the same as provided at the time of client registration.',
    ];

    /**
     * @var int Default code.
     */
    protected $defaultCode = 404;
}
