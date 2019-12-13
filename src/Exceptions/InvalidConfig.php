<?php

namespace Meveto\Client\Exceptions;

class InvalidConfig extends Validation
{
    /**
     * Throws exception if Meveto configuration is not set
     * 
     * @return self
     */
    public static function configNotSet(): self
    {
        return new static("Your Meveto client configuration is not set.");
    }

    /**
     * Throws an exception if a specified architecture is not supported or invalid
     * 
     * @param string $architecture The specified architecture that is not supported or invalid
     * @param array $supported The list of currently supported architectures
     * @return self
     */
    public static function architectureNotSupported(string $architecture, array $supported): self
    {
        $throwable = "`{$architecture}` is not supported. At the moment, supported architectures include: ";
        $count = 1;
        foreach($supported as $arch)
        {
            $throwable .= "`{$arch}`";
            $throwable .= count($supported) == $count ? $throwable .= '.' : $throwable .= ', ';
            $count++;
        }

        return new static($throwable);
    }
}