<?php

namespace Meveto\Client\Services;

use Meveto\Client\Exceptions\Database as MevetoDatabase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Meveto\Client\Models\MevetoUser;

class Database
{
    /** @var */
    protected $drivers = ['mysql'];

    /** @var array */
    protected $dbConnection = [
        'driver'    => 'mysql',
        'host'      => '',
        'database'  => '',
        'username'  => '',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ];

    /**
     * Set access credentials for your applications database. Meveto SDK will use these credentials to communicate with your database.
     * 
     * @param array $credentials The application's databse credentials
     * @return bool
     * 
     * @throws keyNotValid
     * @throws driverNotSupported
     * @throws valueRequiredAt
     */
    public function credentials(array $credentials): bool
    {
        foreach($credentials as $key => $value)
        {
            $value = trim($value);

            if(!in_array($key, $this->dbConnection, true))
            {
                throw MevetoDatabase::keyNotValid('database credentials', $key);
                return false;
            }

            if($key == 'driver')
            {
                if(!in_array($value, $this->drivers))
                {
                    throw MevetoDatabase::driverNotSupported($value, $this->drivers);
                    return false;
                }
            }

            if($value == '' || $value === null)
            {
                if(in_array($key, ['driver', 'host', 'database', 'username', 'password'], true))
                {
                    throw MevetoDatabase::valueRequiredAt('database credentials', $key);
                    return false;
                }
            }

            $this->dbConnection[$key] = $value;
        }
        
        return true;
    }

    /**
     * Opens a database connection so that Meveto SDK can communicate with the client's database
     * 
     * @return void
     * 
     * @throws couldNotConnectToDatabase
     */
    public function establishConnection(): void
    {
        $capsule = new Capsule;

        $capsule->addConnection($this->dbConnection, 'MevetoSDK');
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $connection = $capsule->getConnection('MevetoSDK');

        try {
            $connection->getPdo();
        } catch(\Exception $e)
        {
            throw MevetoDatabase::couldNotConnectToDatabase($this->dbConnection['database']);
        }

        if(! $connection->getSchemaBuilder()->hasTable('meveto_users'))
        {
            throw MevetoDatabase::mevetoUsersTableNotFound();
        }
    }

    /**
     * Login a user for Meveto SDK
     * 
     * @param string $user The user identifier
     * @return void
     * 
     * @throws databaseError
     */
    public function loginUser(string $user): void
    {
        $mevetoUser = MevetoUser::where('user_identifier', $user)->first();

        if($mevetoUser->count() > 0)
        {
            try {

                $mevetoUser->last_logged_in = date('Y-m-d H:i:s');
                $mevetoUser->is_logged_in = 1;
                $mevetoUser->save();

            } catch(\Exception $e)
            {
                throw MevetoDatabase::databaseError($e->getMessage());
            }
        } else {
            // Add a new record
            try {
                MevetoUser::create([
                    'user_identifier' => $user,
                    'last_logged_in' => date('Y-m-d H:i:s'),
                    'last_logged_out' => NULL,
                    'is_logged_in' => 1,
                ]);
            } catch(\Exception $e)
            {
                throw MevetoDatabase::databaseError($e->getMessage());
            }
        }
    }

    /**
     * Logout a user for Meveto SDK
     * 
     * @param string $user The user identifier
     * @return void
     * 
     * @throws databaseError
     * @throws userNotFound
     */
    public function logoutUser(string $user): void
    {
        $mevetoUser = MevetoUser::where('user_identifier', $user)->first();

        if($mevetoUser->count() > 0)
        {
            try {

                $mevetoUser->last_logged_out = date('Y-m-d H:i:s');
                $mevetoUser->is_logged_in = 0;
                $mevetoUser->save();

            } catch(\Exception $e)
            {
                throw MevetoDatabase::databaseError($e->getMessage());
            }
        } else {
            throw MevetoDatabase::userNotFound($user);
        }
    }
}