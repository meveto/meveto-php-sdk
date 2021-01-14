<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class ValueNotValidAtException.
 */
class ValueNotValidAtException extends MevetoException
{
    /**
     * ValueNotValidAtException constructor.
     *
     * @param string $keyLocation
     * @param string $invalidKey
     * @param mixed|null $expectedValue
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $keyLocation, string $invalidKey, $expectedValue = null, $code = 0, Throwable $previous = null)
    {
        // build message.
        $message = $expectedValue ?
            "`{$invalidKey}` has an invalid value inside `{$keyLocation}` array. It must be a valid `{$expectedValue}`"
            :
            "`{$invalidKey}` has an invalid value inside `{$keyLocation}` array.";

        // call parent constructor.
        parent::__construct($message, $code, $previous);
    }
}
