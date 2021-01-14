<?php

namespace Meveto\Client\Exceptions;

use Exception;
use Throwable;

/**
 * Class MevetoException.
 *
 * This classes is a single point of inheritance for exception classes, allowing
 * type-checking for internal/external exceptions.
 */
abstract class MevetoException extends Exception
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [];

    /**
     * @var int Default exception code.
     */
    protected $defaultCode = 0;

    /**
     * @var string Join message lines with a space (or custom if overwritten.
     */
    protected $messageSeparator = ' ';

    /**
     * @var string What string use between entries when quote imploding.
     */
    protected $quoteImplodeSeparator = ', ';

    /**
     * NotAuthorizedException constructor.
     *
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        // call parent constructor.
        parent::__construct($this->buildMessage($message), $this->buildCode($code), $previous);
    }

    /**
     * Build exception message.
     *
     * @param string|null $additionalMessage
     *
     * @return string
     */
    protected function buildMessage($additionalMessage = ''): string
    {
        // if empty string, use null.
        $message = empty($additionalMessage) ? null : $additionalMessage;

        // merge default messages and custom one.
        $lines = array_filter(array_merge($this->defaultMessageLines, [ $message ]));

        // implode message lines.
        return implode($this->messageSeparator, $lines);
    }

    /**
     * @param int $customCode
     *
     * @return int|mixed
     */
    protected function buildCode($customCode = 0): int
    {
        // start code as being default code.
        $code = $this->defaultCode;

        // use custom code if not zero.
        if ($customCode !== 0) {
            $code = $customCode;
        }

        // return code to complete.
        return $code;
    }

    /**
     * Quote implode a list of values.
     *
     * @param array $values
     *
     * @return string
     */
    protected function quoteImplode(array $values): string
    {
        // mapper function.
        $mapper = function ($value) {
            return $this->quoteMessage($value);
        };
        // quote merge
        $quoted = array_map($mapper, $values);

        // return imploded message.
        return implode($this->quoteImplodeSeparator, $quoted);
    }

    /**
     * Quote a given message.
     *
     * @param string $message
     *
     * @return string
     */
    protected function quoteMessage(string $message): string
    {
        return "`{$message}`";
    }
}
