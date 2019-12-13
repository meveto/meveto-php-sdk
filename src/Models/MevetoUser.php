<?php

namespace Meveto\Client\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * This model is responsible for interacting with Meveto Users table/collection on a third party (Meveto client) database.
 */
class MevetoUser extends Model
{
    protected $table = 'meveto_users';

    protected $fillable = [
        'user_identifier',
        'last_logged_in',
        'last_logged_out',
        'is_logged_in',
    ];
    
}