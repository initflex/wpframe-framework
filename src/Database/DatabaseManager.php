<?php

namespace Wpframe\Sys\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseManager
{   
    private static $dbConfig = [];

    /**
     * Start Database Manager
     *
     * @param [type] $databaseConfig
     * @return void
     */
    public static function start($databaseConfig = [])
    {
        self::$dbConfig = $databaseConfig;
        $capsule = new Capsule();

        $capsule->addConnection(
            self::$dbConfig['connections'][self::$dbConfig['default']],
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();   
    }
}

