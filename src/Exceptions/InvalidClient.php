<?php

namespace Meveto\Client\Exceptions;

use Exception;

class InvalidClient extends Exception
{
    public static function clientNotFound(): self
    {
        return new static("Your Meveto client credentials are incorrect. Check your client ID, secret and redirect URL. Redirect URL must be exactly the same as provided at the time of client registration.");
    }

    /**
     * Throws the error message that's received from the Meveto's authorization server
     * 
     * @param string $error The error received from the authorization server
     */
    public static function clientError(string $error): self
    {

        return new static("Meveto authorization server responded with the following error. `{$error}`");
    }
}