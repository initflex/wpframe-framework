<?php

namespace Wpframe\Sys\Exceptions;

use Wpframe\Sys\Http\Request;

/**
 * Visual Errors for Error Handlers
 */
class visualError
{
    public static $getError = false;
    public static $getContentsLines = '';
    /**
     * 
     *
     * @var \Wpframe\Sys\Http\Request
     */
    public static $request;

    /**
     * Start Visual Error
     *
     * @param mixed $error
     * @return \Wpframe\Sys\Exceptions\visualError
     */
    public static function start($error)
    {
        self::$getError = $error;
        self::$request = new Request();
        return new static;
    }

    /**
     * Run Visual Errors
     *
     * @return void
     */
    public function run()
    {
        self::$getContentsLines = wpf_get_contents_lines(
            htmlentities(file_get_contents(self::$getError['file'])), 
            self::$getError['line'], 
            12, 
            12
        );

        $getAcceptHeader = self::$request->getHeaders(['Accept']) ? 
            strtolower(self::$request->getHeaders(['Accept'])['Accept']) : '';
        $pattern = '/application\/json/i';

        if(!preg_match($pattern, $getAcceptHeader)) {
            // Displays the error message in the form of an HTML response
            require_once __DIR__ .'/src/views/visual_error.php';
            exit();
        }
    }
}