<?php

namespace Wpframe\Sys\Support;

class Session
{
    private static $dataSess = false;
    private static $session = false;
    private static $flashsession = false;
    private static $sessionName = false;

    /**
     * Start Session
     *
     * @param array $config
     * @return void
     */
    public static function start($config = [])
    {   
        //Set save path session
        if(isset($config['save_path']) && is_dir($config['save_path'])) {
            @ini_set('session.save_path', $config['save_path']);
        }

        //Set the maxlifetime of the session
        @ini_set("session.gc_maxlifetime", $config['lifetime']);

        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /**
     * Set Session
     *
     * @param string|array $session_name
     * @param mixed $session_val
     * @return mixed
     */
    public static function set_session($session_name = NULL, $session_val = NULL)
    {
        if ($session_name !== NULL && is_array($session_name) && count($session_name) > 0) {
            // array method is set
            // is data session
            self::$dataSess = $session_name;
            foreach (self::$dataSess as $name => $val) {
                if (trim($name) !== '' && trim($val) !== '') {
                    // set session
                    $_SESSION[$name] = $val;
                }
            }
        } else {
            // array method not set
            if ($session_name !== NULL && !empty($session_name)) {
                if ($session_val !== NULL && !empty($session_val)) {
                    // set session
                    $_SESSION[$session_name] = $session_val;
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Get Session by Name
     *
     * @param string $session_name
     * @return mixed
     */
    public static function get_session($session_name = NULL)
    {
        if ($session_name !== NULL && !empty($session_name)) {
            self::$session = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : FALSE;
            return self::$session;
        } else {
            return FALSE;
        }
    }

    /**
     * Set Flash Session
     *
     * @param string $session_name
     * @param string $session_val
     * @return mixed
     */
    public static function set_flashsession($session_name = NULL, $session_val = NULL)
    {
        // check name and val session
        if ($session_name !== NULL && !empty($session_name)) {
            if ($session_val !== NULL && !empty($session_val)) {
                // set flash session
                $_SESSION[$session_name] = $session_val;
                self::$session = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : FALSE;
                return self::$session;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Run Flash Session
     *
     * @param string $session_name
     * @return mixed
     */
    public static function flashsession($session_name = NULL)
    {
        if ($session_name !== NULL && !empty($session_name)) {
            // get data flash session
            self::$flashsession = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : FALSE;
            // unset session
            if (isset($_SESSION[$session_name])) {
                unset($_SESSION[$session_name]);
            }
            return self::$flashsession;
        } else {
            return FALSE;
        }
    }

    /**
     * Unset Session by Name
     *
     * @param array|string $session_name
     * @return mixed
     */
    public static function unset_session($session_name = NULL)
    {
        if ($session_name !== NULL && is_array($session_name) && count($session_name) > 0) {
            self::$sessionName = $session_name;
            foreach (self::$sessionName as $name) {
                if (isset($_SESSION[$name]) && trim($_SESSION[$name]) !== '') {
                    // unset session
                    unset($_SESSION[$name]);
                }
            }
        } else {
            // session name is not array
            if ($session_name !== NULL) {
                if (isset($_SESSION[$session_name])) {
                    // unset session
                    unset($_SESSION[$session_name]);
                }
            } else {
                return FALSE;
            }
        }
    }
}
