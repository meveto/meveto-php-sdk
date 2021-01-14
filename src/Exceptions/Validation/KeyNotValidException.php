<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class KeyNotValidException.
 */
class KeyNotValidException extends MevetoException
{
    /**
     * KeyNotValidException constructor.
     *
     * @param string $keyLocation
     * @param string $invalidKey
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $keyLocation, string $invalidKey, $code = 0, Throwable $previous = null)
    {
        // build message.
        $message = "Your `{$keyLocation}` array has an unexpected key `{$invalidKey}`.";

        // call parent constructor.
        parent::__construct($message, $code, $previous);
    }
}
