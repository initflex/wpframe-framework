<?php

namespace Wpframe\Sys\Support;

use eftec\bladeone\BladeOne;

/**
 * View - BladeOne
 */
class View
{
    public static $viewsDir;
    public static $compileDir;
    public static $mode;
    /**
     * Undocumented variable
     *
     * @var bool|\eftec\bladeone\BladeOne
     */
    public static $blade = false;

    public function __construct()
    {
        self::$viewsDir = wpf_base_path('/resources/views');
        self::$compileDir = wpf_storage_path('/plugins/views');
        self::$mode = BladeOne::MODE_AUTO;
    }

    /**
     * Undocumented function
     *
     * @param boolean $viewsPath
     * @param boolean $compileDir
     * @param boolean $mode
     * @return \eftec\bladeone\BladeOne|bool
     */
    public static function init($viewsPath = false, $compileDir = false, $mode = false)
    {
        self::$viewsDir = $viewsPath ? $viewsPath : self::$viewsDir;
        self::$compileDir = $compileDir ? $compileDir : self::$compileDir;
        self::$mode = $mode ? $mode : self::$mode;

        // MODE_DEBUG allows to pinpoint troubles.
        if(is_dir(self::$viewsDir) && is_dir(self::$compileDir)) {
            self::$blade = new BladeOne(
                self::$viewsDir,
                self::$compileDir,
                self::$mode
            );
        }
    }

    public static function make($view, $data = [])
    {
        self::init();
        if(self::$blade) {
            return self::$blade->run($view, $data);
        }
    }
}