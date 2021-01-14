<?php

namespace Meveto\Client\Exceptions\InvalidConfig;

use Meveto\Client\Exceptions\MevetoException;
use Throwable;

/**
 * Class ArchitectureNotSupportedException.
 */
class ArchitectureNotSupportedException extends MevetoException
{
    /**
     * @var string[] Default message lines.
     */
    protected $defaultMessageLines = [];

    /**
     * ArchitectureNotSupportedException constructor.
     *
     * @param string $architecture
     * @param array $supported
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $architecture, array $supported = [], $code = 0, Throwable $previous = null)
    {
        // supported list.
        $supportedList = implode(', ', $supported);

        // build custom message prefix.
        $prefix = "`{$architecture}` is not supported.";
        // build custom message.
        $message = "{$prefix} At the moment, supported architectures include: {$supportedList}.";

        // call parent constructor.
        parent::__construct($message, $code, $previous);
    }
}
