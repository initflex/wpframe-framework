<?php

namespace Wpframe\Sys\Support;

/**
 * Configuration for applications
 */
class Config
{
    public static $config = [];
    public static $configFiles = [
        'app.php',
        'logging.php',
        'security.php',
        'session.php',
        'database.php',
        'providers.php',
    ];

    /**
     * Load the configuration file
     *
     * @param array $configFiles
     * @return array
     */
    public static function configLoad($configFiles = [])
    {
        self::$configFiles = array_merge(self::$configFiles, $configFiles);
        // unique array
        self::$configFiles = array_unique(self::$configFiles);
        self::mergeConfigFiles(self::$configFiles);
        return self::$config;
    }

    /**
     * Combine configuration items
     *
     * @param array $configFiles Add Configuration file
     * @return array
     */
    private static function mergeConfigFiles($configFiles = [])
    {
        foreach ($configFiles as $configFile) {
            $fileSet = wpf_base_path('/config/'. $configFile);
            $fileNameArr = explode('.', $configFile);
            $setConf = [$fileNameArr[0] => (require $fileSet)];
            self::$config = array_merge(self::$config, $setConf);
        }
        return self::$config;
    }

    /**
     * Get Configuration Items
     *
     * @param string|null $configSelector
     * @return void
     */
    public static function config($configSelector = null)
    {
        if ($configSelector == null) {
            return false;
        }

        $array = self::$config;
        $keys = explode('.', $configSelector);
    
        // Iteration from key to get the value
        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                // Set default value if key not found
                return false;
            }
        }
        return $array;
    }
}