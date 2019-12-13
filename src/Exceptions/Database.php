<?php

namespace Meveto\Client\Exceptions;

class Database extends Validation
{
    /**
     * Throws exception if database credentials are not set
     * 
     * @return self
     */
    public static function databaseNotSet(): self
    {
        return new static("Your database connection credentials are not set.");
    }

    /**
     * Throws an exception if a specified database driver is not yet supported by this SDK
     * 
     * @param string $driver The specified driver that is not supported
     * @param array $supported The list of currently supported drivers
     * @return self
     */
    public static function driverNotSupported(string $driver, array $supported): self
    {
        $throwable = "`{$driver}` is not supported. At the moment, supported database drivers include: ";
        $count = 1;
        foreach($supported as $db)
        {
            $throwable .= "`{$db}`";
            $throwable .= count($supported) == $count ? $throwable .= '.' : $throwable .= ', ';
            $count++;
        }

        return new static($throwable);
    }

    /**
     * Throws an exception if an error occurs while trying to connect to the specified database using the provided credentials
     * 
     * @param string $database Name of the database to which the connection could not succeed.
     * @return self
     */
    public static function couldNotConnectToDatabase(string $database): self
    {
        return new static("Meveto could not connect to database `{$database}`. Check the database credentials you have provided.");
    }

    /**
     * Throws an exception if the required "meveto_users" table is not found
     * 
     * @return self
     */
    public static function mevetoUsersTableNotFound(): self
    {
        return new static("`meveto_users` table not found. Make sure you have added `meveto_users` table to your database.");
    }

    /**
     * Throws an exception if a database error occurs
     * 
     * @param string $error The error description
     * @return self
     */
    public static function databaseError(string $error): self
    {
        return new static($error);
    }

    /**
     * Throws an exception if a Meveto user identifier is not found in the database
     * 
     * @param string $user The user identifier that could not be found
     * @return self
     */
    public static function userNotFound(string $user): self
    {
        return new static("The specified user `{$user}` could not be found in the `meveto_users` table.");
    }
}