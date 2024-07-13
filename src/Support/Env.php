<?php

namespace Wpframe\Sys\Support;

use Dotenv\Dotenv;

class Env
{
    public static $loadDirectoryEnv = __DIR__ .'/../../../';
    
    /**
     * Start and load environment file
     *
     * @return void
     */
    public static function start()
    {
        $dotenv = Dotenv::createMutable(self::$loadDirectoryEnv);
        $dotenv->load();
    }

    /**
     * Get environment variable
     *
     * @param string $envName
     * @return void
     */
    public static function get($envName = '', $default = null)
    {
        if (trim($envName) == '') return $default;
        if(isset($_ENV[$envName])) {
            $setEnvValue = trim($_ENV[$envName]);
            if (strtolower($setEnvValue) == 'false') return false;
            if (strtolower($setEnvValue) == 'null') return null;
            return $setEnvValue;
        }
        
        return $default;
    }
}
