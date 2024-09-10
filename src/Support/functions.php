<?php

use Wpframe\Sys\Support\Env;
use Wpframe\Sys\Support\Config;
use Wpframe\Sys\Support\Response;
use Wpframe\Sys\Container\Container;
use Wpframe\Sys\Support\View;

/**
 * WordPress initialization - Add Action
 *
 * @param mixed $callback
 * @param integer $priority
 * @param integer $accepted_args
 * @return void
 */
function wpf_wpinit($callback, int $priority = 10, $accepted_args = 1)
{
    add_action('init', $callback, $priority, $accepted_args);
}

/**
 * Dump and Die
 *
 * @param mixed $variable
 * @return void
 */
function wpf_dd($variable) {
    var_dump($variable);
    die();
}

function wpf_view($view, $data = [])
{
    return View::make($view, $data);
}

/**
 * Get Instance of WP_User
 *
 * @param integer $userId
 * @param string $name
 * @param string|integer $site_id
 * @return \WP_User
 */
function wpf_wpuser(int $userId = 0, $name = '', $site_id = '')
{
    return new \WP_User($userId, $name, $site_id);
}

/**
 * Get PHP Version Number
 *
 * @return mixed
 */
function wpf_php_version()
{
    if (function_exists("phpversion")) return phpversion();
    return null;
}

/**
 * Set PUT Method
 *
 * @return string
 */
function wpf_form_put()
{
    return '<input type="hidden" name="_inputMethod" value="PUT" />';
}

/**
 * Set DELETE Method
 *
 * @return string
 */
function wpf_form_delete()
{
    return '<input type="hidden" name="_inputMethod" value="DELETE" />';
}

/**
 * Set PATCH Method
 *
 * @return string
 */
function wpf_form_patch()
{
    return '<input type="hidden" name="_inputMethod" value="PATCH" />';
}

/**
 * Set OPTIONS Method
 *
 * @return string
 */
function wpf_form_options()
{
    return '<input type="hidden" name="_inputMethod" value="OPTIONS" />';
}

/** 
 * request fullscreen page
 */
function wpf_fullscreen()
{
    @ob_end_clean();
}

/** 
 * end request fullscreen page
 */
function wpf_fullscreen_end()
{
    @exit();
}

/** 
 * Redirect - Default Header Function
 * @param string $url  for set url redirect
 * @param integer $permanent  For Set Redirect Status Code  
 */
function wpf_redirect_url($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

/** 
 * Redirect Admin url
 * @param string  $pageName  for set page name
 * @param string  $methodName   For Set Method Name
 * @param array  $paramsSet  For Set Parameters Redirect - Default Empty Array
 * @param integer  $statusCode  For set Status Code Redirect - Default Status Code 301
 * @return false|void  
 */
function wpf_admin_redirect($pageName = null, $methodName = null, $paramsSet = [], $statusCode = 301)
{
    $adminUrl = admin_url('admin.php');
    $pageFormatUrl = '?page=';
    $methodUrl = '&m=';
    $setUrl = '';

    if (is_array($paramsSet) && count($paramsSet) > 0) {
        $paramSetTemp = '';
        foreach ($paramsSet as $key => $value) {
            $paramSetTemp .= '&' . $key . '=' . $value;
        }
        $paramsSetData = $paramSetTemp;
    } else {
        $paramsSetData = '';
    }

    if (
        $pageName !== null && $pageName !== '' &&
        $methodName !== null && $methodName !== ''
    ) {
        $setUrl = $adminUrl . $pageFormatUrl . $pageName . $methodUrl . $methodName . $paramsSetData;
        wpf_redirect_url($setUrl, $statusCode);
    } else {
        return FALSE;
    }
}

/** 
 * Create an Admin URL
 * @param string  $pageName  for set page name
 * @param string  $methodName   For Set Method Name
 * @param array  $paramsSet  For Set Parameters Redirect - Default Empty Array
 * @return false|string  
 */
function wpf_admin_url($pageName = null, $methodName = null, $paramsSet = [])
{
    $adminUrl = admin_url('admin.php');
    $pageFormatUrl = '?page=';
    $methodUrl = '&m=';
    $setUrl = '';

    if (is_array($paramsSet) && count($paramsSet) > 0) {
        $paramSetTemp = '';
        foreach ($paramsSet as $key => $value) {
            $paramSetTemp .= '&' . $key . '=' . $value;
        }
        $paramsSetData = $paramSetTemp;
    } else {
        $paramsSetData = '';
    }

    if (
        $pageName !== null && $pageName !== '' &&
        $methodName !== null && $methodName !== ''
    ) {
        $setUrl = $adminUrl . $pageFormatUrl . $pageName . $methodUrl . $methodName . $paramsSetData;
        return $setUrl;
    } else {
        return FALSE;
    }
}

/**
 * Get Method name From URL
 *
 * @return string  Default Method is 'index'.
 */
function wpf_get_url_method()
{
    if(isset($_GET['m']) && trim(htmlentities($_GET['m'])) !== '') {
        return trim(htmlentities($_GET['m']));
    }

    return 'index';
}

/**
 * Get domain url
 *
 * @param string $path
 * @return void
 */
function wpf_get_domain_url(string $path = '')
{
    return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://". (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : (
        isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : ''
    )) ."$path";
}

/**
 * Remove slash in first string. works across both Windows and Unix
 *
 * @param string $url
 * @return void
 */
function wpf_remove_first_slash(string $url = '')
{
    $url = ltrim($url, '/\\');
    return $url;
}

/**
 * Remove slash in last string. works across both Windows and Unix
 *
 * @param string $url
 * @return void
 */
function wpf_remove_last_slash(string $url = '')
{
    $url = rtrim($url, '/\\');
    return $url;
}

/**
 * Force HTTP Status code
 *
 * @param string $code
 * @param string $description
 * @return void
 */
function wpf_status_code($code = '200', $description = '')
{
    if ($description == '' && $code == '200') {
        $description = 'OK';
    }
    @http_response_code($code);
    @header("Status: $code $description");
}

/**
 * Get environment variable.
 * Returns environment variable from Env::get()
 *
 * @param string $envName
 * @param mixed $default
 * @return string|bool|null
 */
function wpf_env($envName = '', $default = null)
{
    return Env::get($envName, $default);
}

/**
 * Wpf base path.
 * Use a slash at the beginning of your join path.
 *
 * @param string $joinPath
 * @return string
 */
function wpf_base_path($joinPath = '')
{
    return WPFP_BASE_PATH . $joinPath;
}

/**
 * Wpf storage path.
 * Use a slash at the beginning of your join path.
 *
 * @param string $joinPath
 * @return string
 */
function wpf_storage_path($joinPath = '')
{
    return wpf_base_path('/storage' . $joinPath);
}

/**
 * Wpf public path.
 * Use a slash at the beginning of your join path.
 *
 * @param string $joinPath
 * @return string
 */
function wpf_public_path($joinPath = '')
{
    return wpf_base_path('/public' . $joinPath);
}

/**
 * Wpf bootstrap path.
 * Use a slash at the beginning of your join path.
 *
 * @param string $joinPath
 * @return string
 */
function wpf_bootstrap_path($joinPath = '')
{
    return wpf_base_path('/bootstrap' . $joinPath);
}

/**
 * Wpf system path.
 * Use a slash at the beginning of your join path.
 *
 * @param string $joinPath
 * @return string
 */
function wpf_system_path($joinPath = '')
{
    return __DIR__ . '/..'. $joinPath;
}

/**
 * Make a file and write content to file.
 *
 * @param boolean $filePath
 * @param string $content
 * @return int
 * @return bool false = filePath not set or cant create file
 * @return int 1 = successful to create file, file not writeable
 * @return int 2 = successful to create file and file writeable
 */
function wpf_write_file($filePath = false, $content = '', $fileMode = 'w'): int|bool
{
    if ($filePath !== false) {
        $openFile = fopen($filePath, $fileMode);
        fwrite($openFile, $content);
        fclose($openFile);

        if (file_exists($filePath)) {
            if (is_writeable($filePath)) {
                return 2;
            }
            return 1;
        }
        return false;
    }
    return false;
}

/**
 * Unique id generator
 *
 * @param integer $lenght
 * @return string
 */
function wpf_unique_id($lenght = 13)
{
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

/**
 * Get contents from selected lines
 *
 * @param string $contents
 * @param integer $lineStart
 * @param integer $topOffset
 * @param integer $bottomOffset
 * @return mixed
 */
function wpf_get_contents_lines($contents, int $lineStart, $topOffset = 7, $bottomOffset = 7)
{
    if (!$lineStart) {
        return null;
    }

    $getContentPerLine = preg_split("/\n/", $contents);
    $countContentLines = is_array($getContentPerLine) ? count($getContentPerLine) : 0;
    if ($countContentLines <= 0 && !is_array($getContentPerLine)) {
        return $contents;
    }

    if ($countContentLines < $lineStart) {
        return false;
    }

    $lineStartContent = '';
    if (isset($getContentPerLine[($lineStart - 1)])) {
        $lineStartContent = $getContentPerLine[($lineStart - 1)];
    }

    // Get content top offset
    $getContentTopOffset = '';
    $topOffsetNum = $lineStart - $topOffset;
    if($topOffsetNum >= 0) {
        $startGetContentTop = $topOffsetNum;
        while (($lineStart - 1) > $startGetContentTop) {
            $newLine = ($lineStart - 1) == $startGetContentTop ? "" : "\n";
            $getContentTopOffset .= $getContentPerLine[$startGetContentTop] . $newLine;
            $startGetContentTop++;
        }
    }
    // Get content top offset - start from 0
    if($topOffsetNum < 0) {
        $startGetContentTop = 0;
        while (($lineStart - 1) > $startGetContentTop) {
            $newLine = ($lineStart - 1) == $startGetContentTop ? "" : "\n";
            $getContentTopOffset .= $getContentPerLine[$startGetContentTop] . $newLine;
            $startGetContentTop++;
        }
    }

    $topEndNum = $topOffsetNum;

    // Get content bottom offset
    $getContentBottomOffset = '';
    $bottomOffsetNum = $lineStart + $bottomOffset;
    if($bottomOffsetNum <= $countContentLines) {
        $startGetContentBottom = $lineStart;
        while ($startGetContentBottom < $bottomOffsetNum) {
            $newLine = $startGetContentBottom == $bottomOffsetNum ? "" : "\n";
            $getContentBottomOffset .= $getContentPerLine[$startGetContentBottom] . $newLine;
            $startGetContentBottom++;
        }
    }

    // Get content top offset - start from $lineStart to last line
    if($bottomOffsetNum > $countContentLines) {
        $startGetContentBottom = $lineStart;
        while ($countContentLines > $startGetContentBottom) {
            $newLine = $countContentLines == $startGetContentBottom ? "" : "\n";
            $getContentBottomOffset .= $getContentPerLine[$startGetContentBottom] . $newLine;
            $startGetContentBottom++;
        }
    }

    $bottomEndNum = $countContentLines;

    return [
        'contents'  =>  $getContentTopOffset . $lineStartContent . $getContentBottomOffset,
        'line_start'=>  $lineStart,
        'top_num'   =>  $topEndNum,
        'bottom_num'=>  $bottomEndNum,
    ];
}

/**
 * Duplicate backslash
 *
 * @param string|null $string
 * @return void
 */
function duplicateBackSlash($string = null)
{
    if ($string == null) { return $string; }
    return str_replace('\\', '\\\\', $string);
}

/**
 * Get All Cookies
 *
 * @return array
 */
function getAllCookies()
{
    if (isset($_COOKIE)) {
        return $_COOKIE;
    }
    return [];
}

/**
 * Get All Sessions
 *
 * @return array
 */
function getAllSessions()
{
    if (isset($_SESSION)) {
        return $_SESSION;
    }
    return [];
}

/**
 * Get WordPress Version from Global Variables
 *
 * @return mixed
 */
function wpf_wp_version()
{
    global $wp_version;
    return $wp_version;
}

/**
 * Get value from configuration
 *
 * @param string|null $selector
 * @return mixed
 */
function wpf_config($selector = null)
{
    return Config::config($selector);
}

/**
 * Get file path by class namespace
 *
 * @param boolean $classNamespace
 * @return string
 */
function wpf_class_dir($classNamespace = false)
{
    $removeFirstPathNamespace = str_replace(
        ['Wpframe\\Sys\\', 'Wpframe\\'], 
        ['', ''], 
        $classNamespace);
    $classPathToDir = str_replace('\\', '/', $removeFirstPathNamespace);
    return $classPathToDir;
}

/**
 * Get item by container::make() or return Container object
 *
 * @param string $identifier
 * @return mixed|\Wpframe\Sys\Container\Container
 */
function wpf_app($identifier = null)
{
    if ($identifier == null) {
        return Container::start();
    }
    return Container::make($identifier);
}

/**
 * Array to Json
 *
 * @param array $array
 * @return string
 */
function wpf_to_json($array = [], $secondArg = null)
{
    $jsonSecondArg = $secondArg == null ? JSON_FORCE_OBJECT : $secondArg;
    $toJson = json_encode($array, $jsonSecondArg);
    return $toJson;
}

/**
 * Response Instantiation
 *
 * @return \Wpframe\Sys\Support\Response
 */
function wpf_response()
{
    return Response::start();
}