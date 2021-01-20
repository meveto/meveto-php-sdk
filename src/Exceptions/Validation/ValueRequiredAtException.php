<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class ValueRequiredAtException.
 */
class ValueRequiredAtException extends MevetoException
{
    /**
     * ValueRequiredAtException constructor.
     *
     * @param string $keyLocation
     * @param string $invalidKey
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $keyLocation, string $invalidKey, $code = 0, Throwable $previous = null)
    {
        // build message.
        $message = "`{$invalidKey}` is required inside `{$keyLocation}` array and it can not be empty or null.";

        // call parent constructor.
        parent::__construct($message, $code, $previous);
    }
}
