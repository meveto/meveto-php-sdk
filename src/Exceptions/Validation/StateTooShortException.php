<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class StateTooShortException.
 */
class StateTooShortException extends MevetoException
{
    /**
     * StateTooShortException constructor.
     *
     * @param int $length
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($length = 128, $code = 0, Throwable $previous = null)
    {
        // build message.
        $message = "Current application request state must be at least `{$length}` characters long.";

        // call parent constructor.
        parent::__construct($message, $code, $previous);
    }
}
