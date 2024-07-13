<?php

namespace Wpframe\Sys\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

/**
 * Create your logs with Logger
 */
class Log
{
    /**
     * Logger Class
     *
     * @var \Monolog\Logger
     */
    public static $logger;
    public static $logName = 'wpframe';
    public static $logLevel = 'debug';
    public static $logChannel = 'rotate';
    public static $logConfigFile = '/config/logging.php';
    public static $logConfig = [];
    public static $logFile = '/logs/wpframe.log';

    /**
     * Start running the log
     *
     * @param null|string $logConfigFile
     * @return \Wpframe\Sys\Logger\Log
     */
    public static function start($logConfigFile = null)
    {
        self::$logFile = wpf_storage_path(self::$logFile);
        self::$logConfigFile = $logConfigFile !== null ? $logConfigFile : self::$logConfigFile;
        self::$logConfig = (require wpf_base_path(self::$logConfigFile));
        self::channel();
        self::level();
        return new static;
    }

    /**
     * Run Logger
     *
     * @param boolean|string $name
     * @param array $handlers
     * @param array $processors
     * @param \DateTimeZone|null $timezone
     * @return \Wpframe\Sys\Logger\Log
     */
    public static function logger($name = false, $handlers = [], $processors = [], $timezone = null)
    {
        self::$logName = $name ? $name : self::$logName;
        self::$logger = new Logger(self::$logName, $handlers, $processors, $timezone);
        return new static;
    }

    /**
     * Set Log Channel
     *
     * @param boolean|string $channelName
     * @return \Wpframe\Sys\Logger\Log
     */
    public static function channel($channelName = false)
    {
        $getConfChName = self::$logConfig['default'];
        self::$logChannel = $channelName ?
            $channelName : (isset($getConfChName) && trim($getConfChName) !== '' ?
                $getConfChName : self::$logChannel);
        return new static;
    }

    /**
     * Set Log Level
     *
     * @param boolean|string $level
     * @return \Wpframe\Sys\Logger\Log
     */
    public static function level($level = false)
    {
        $getConfChLvl = isset(self::$logConfig['channels'][self::$logChannel]['level']) ?
            self::$logConfig['channels'][self::$logChannel]['level'] : false;
        self::$logLevel = $level ?
            $level : ($getConfChLvl && trim($getConfChLvl) !== '' ?
                $getConfChLvl : self::$logLevel);
        return new static;
    }

    /**
     * List of Log Levels
     *
     * @param mixed $level
     * @return mixed
     */
    private static function listOfLevels($level = false)
    {
        if (!$level) return self::listOfLevels(self::$logLevel);

        $levelLists = [
            'debug' => Logger::DEBUG,
            'info' => Logger::INFO,
            'notice' => Logger::NOTICE,
            'warning' => Logger::WARNING,
            'error' => Logger::ERROR,
            'critical' => Logger::CRITICAL,
            'alert' => Logger::ALERT,
            'emergency' => Logger::EMERGENCY,
        ];

        return isset($levelLists[$level]) ?
            $levelLists[$level] : Logger::DEBUG;
    }

    /**
     * Stores logs to files
     *
     * @return \Monolog\Logger
     */
    public static function pushHandler()
    {
        self::$logFile = isset(self::$logConfig['channels'][self::$logChannel]['path']) ?
            self::$logConfig['channels'][self::$logChannel]['path'] : self::$logFile;
        if (self::$logChannel == 'rotate') {
            $itemPush = new RotatingFileHandler(self::$logFile, 31, self::listOfLevels(self::$logLevel));
            $itemPush->setFilenameFormat('{filename}_{date}', 'Y-m-d');
        } else {
            $itemPush = new StreamHandler(self::$logFile, self::listOfLevels(self::$logLevel));
        }
        return self::$logger->pushHandler($itemPush);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function debug($msg = '', $context = [])
    {
        return self::$logger->debug($msg, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function info($msg = '', $context = [])
    {
        return self::$logger->info($msg, $context);
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function notice($msg = '', $context = [])
    {
        return self::$logger->notice($msg, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function warning($msg = '', $context = [])
    {
        return self::$logger->warning($msg, $context);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function error($msg = '', $context = [])
    {
        return self::$logger->error($msg, $context);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function critical($msg = '', $context = [])
    {
        return self::$logger->critical($msg, $context);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function alert($msg = '', $context = [])
    {
        return self::$logger->alert($msg, $context);
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * @param string $msg
     * @param array $context
     * @return void
     */
    public static function emergency($msg = '', $context = [])
    {
        return self::$logger->emergency($msg, $context);
    }
}
