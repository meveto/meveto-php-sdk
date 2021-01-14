<?php

namespace Meveto\Client\Exceptions\Validation;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class InputDataInvalidException.
 */
class InputDataInvalidException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [
        'The following errors occurred while processing your request:'
    ];

    /***
     * InputDataInvalidException constructor.
     *
     * @param array $errors
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $errors = [], $code = 0, Throwable $previous = null)
    {
        // build errors list message.
        $message = implode(', ', $errors);

        // call parent.
        parent::__construct("({$message})", $code, $previous);
    }
}
