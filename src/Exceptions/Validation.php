<?php

namespace Meveto\Client\Exceptions;

use Exception;

class Validation extends Exception
{
    /**
     * Throws exception for a missing value that is required
     * 
     * @param string $keyLocation Location where the exception occured.
     * @param string $invalidKey
     * @return self
     */
    public static function valueRequiredAt(string $keyLocation, string $invalidKey): self
    {
        return new static("`{$invalidKey}` is required inside `{$keyLocation}` array and it can not be empty or null.");
    }

    /**
     * Throws exception for an invalid value inside a configuration array
     * 
     * @param string $keyLocation Location where the exception occured.
     * @param string $invalidKey
     * @param mixed $expectedValue
     * @return self
     */
    public static function valueNotValidAt(string $keyLocation, string $invalidKey, $expectedValue = null): self
    {
        $throwable = $expectedValue ?
            "`{$invalidKey}` has an invalid value inside `{$keyLocation}` array. It must be a valid `{$expectedValue}`"
            :
            "`{$invalidKey}` has an invalid value inside `{$keyLocation}` array.";

        return new static($throwable);
    }

    /**
     * Throws exception for an invalid key inside a configuration array
     * 
     * @param string $keyLocation Location where the exception occured.
     * @param string $invalidKey
     * @return self
     */
    public static function keyNotValid(string $keyLocation, string $invalidKey): self
    {
        return new static("Your `{$keyLocation}` array has an unexpected key `{$invalidKey}`.");
    }

    /**
     * Throws exception if input data validation errors are returned by Meveto server
     * 
     * @param array $errors The errors returned
     * @return self
     */
    public static function inputDataInvalid(array $errors): self
    {
        $throwable = "The following errors occurred while processing your request: (";
        $count = 1;
        foreach($errors as $error)
        {
            $throwable .= "`{$error}`";
            $throwable .= count($errors) == $count ? ')' : ', ';
            $count++;
        }
        return new static($throwable);
    }

    /**
     * Throws exception if current application request state is empty
     * 
     * @return self
     */
    public static function stateRequired(): self
    {
        return new static("Current application request state can not be empty.");
    }

    /**
     * Throws exception if current application request state is considered short.
     * 
     * @param string $length Required length of the value
     * @return self
     */
    public static function stateTooShort(string $length): self
    {
        return new static("Current application request state must be at least `{$length}` characters long.");
    }
}