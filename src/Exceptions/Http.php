<?php

namespace Meveto\Client\Exceptions;

use Exception;

class Http extends Exception
{
    public static function notAuthenticated(): self
    {
        return new static("Meveto server could not authenticate your request and responded with a 401 status. Either an access token is missing or the provided token is not valid.");
    }

    public static function notAuthorized(): self
    {
        return new static("The specified access token is not authorized to access the requested information");
    }
}