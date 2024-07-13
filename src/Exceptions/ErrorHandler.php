<?php

namespace Wpframe\Sys\Exceptions;

use Wpframe\Sys\Logger\Log;

class ErrorHandler
{
    public static $appConf = [];

    /**
     * Start error handler
     *
     * @return void
     */
    public function __construct()
    {
        register_shutdown_function([$this, 'fatalErrorHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Get App Configuration
     *
     * @return void
     */
    public function getConfig()
    {
        self::$appConf = (require wpf_base_path('/config/app.php'));
    }

    /**
     * Write errors to log
     *
     * @param mixed $msg
     * @return void
     */
    public static function logHandler($msg)
    {
        Log::debug($msg['message']);
    }

    /**
     * Fatal error handler
     *
     * @return void
     */
    public function fatalErrorHandler()
    {
        $this->getConfig();
        $error = error_get_last();
        if (isset($error['type']) && $error['type'] !== null) {
            self::logHandler($error);
            $error['stack_trace_data'] = $this->stackTraceData($error['message']);
            $error['type_name'] = $this->errorList($error['type']);
            $error['exit_status'] = true;
            if(self::$appConf['app_debug'] == 'true') {
                visualError::start($error)->run();
            }
        }
    }

    /**
     * Stack Trace Errors
     *
     * @param string $errorMessage
     * @return array
     */
    public function stackTraceData($errorMessage = '')
    {
        if ($errorMessage === '') {
            return array();
        }

        $getItemsStackTrace = explode("\n", $errorMessage);
        $isArrStackTrace = is_array($getItemsStackTrace);
        $countItemsStackTrace = count($getItemsStackTrace);

        if ($isArrStackTrace && $countItemsStackTrace > 0) {
            $x = 0;
            foreach ($getItemsStackTrace as $itemStackTrace) {
                if ($itemStackTrace == 'Stack trace:') {
                    unset($getItemsStackTrace[$x]);
                    break;
                }
                unset($getItemsStackTrace[$x]);
                $x++;
            }

            $getItemsStackTrace = array_values($getItemsStackTrace);

            $y = 0;
            foreach ($getItemsStackTrace as $itemStackTrace) {
                $itemStackTraceTemp = preg_split('/#\d+\s/', $itemStackTrace, -1, PREG_SPLIT_NO_EMPTY);
                $getItemsStackTrace[$y] = isset($itemStackTraceTemp[0]) ? trim($itemStackTraceTemp[0]) : '';
                $y++;
            }

            $z = 0;
            foreach ($getItemsStackTrace as $itemStackTrace) {
                $filePath = preg_split('/\(\d+\):\s/', $itemStackTrace, -1, PREG_SPLIT_NO_EMPTY);
                $pattern = '/\b(\d+)\b(?=[^\(]*\))/';
                $getLine = '';
                if (preg_match($pattern, $itemStackTrace, $matches)) {
                    $getLine = $matches[1];
                }

                $getItemsStackTrace[$z] = [
                    'file_path' => isset($filePath[0]) ? $filePath[0] : '',
                    'line' => $getLine,
                ];

                $z++;
            }
        }
        return $getItemsStackTrace;
    }

    /**
     * List of Error Message Types
     *
     * @param boolean|mixed $errorCode
     * @return mixed
     */
    public function errorList($errorCode = false)
    {
        if ($errorCode == false) {
            return 'UNDEFINED';
        }
        $exceptions = [
            E_ERROR => "E_ERROR",
            E_WARNING => "E_WARNING",
            E_PARSE => "E_PARSE",
            E_NOTICE => "E_NOTICE",
            E_CORE_ERROR => "E_CORE_ERROR",
            E_CORE_WARNING => "E_CORE_WARNING",
            E_COMPILE_ERROR => "E_COMPILE_ERROR",
            E_COMPILE_WARNING => "E_COMPILE_WARNING",
            E_USER_ERROR => "E_USER_ERROR",
            E_USER_WARNING => "E_USER_WARNING",
            E_USER_NOTICE => "E_USER_NOTICE",
            E_STRICT => "E_STRICT",
            E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
            E_DEPRECATED => "E_DEPRECATED",
            E_USER_DEPRECATED => "E_USER_DEPRECATED",
            E_ALL => "E_ALL"
        ];

        if (!isset($exceptions[$errorCode])) {
            return 'UNDEFINED';
        }

        return $exceptions[$errorCode];
    }

    /**
     * Error handler - set_error_handler()
     *
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return void
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->getConfig();
        $error = [
            "type" => $errno,
            "message" => "Error: [$errno] $errstr in $errfile on line $errline",
            "file" => $errfile,
            "line" => $errline,
            'type_name' => $this->errorList($errno),
        ];

        self::logHandler($error);

        $error['message'] = $errstr;
        $error['exit_status'] = false;
        
        if(self::$appConf['app_debug'] == 'true') {
            visualError::start($error)->run();
        }
    }
}
