<?php

namespace Meveto\Client\Exceptions\InvalidClient;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class ClientErrorException.
 */
class ClientErrorException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'Meveto authorization server responded with the following error.',
    ];

    /**
     * @var int Default code.
     */
    protected $defaultCode = 500;
}
