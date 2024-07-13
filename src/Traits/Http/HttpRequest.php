<?php

namespace Wpframe\Sys\Traits\Http;

trait HttpRequest
{
    public $get, $post, $files;

    /**
     * Get Type of the request
     *
     * @return string
     */
    public static function requestType(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get url path
     *
     * @return string
     */
    public static function urlPathInfo(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Get full url from the request
     *
     * @return string
     */
    public static function requestFullUrl(): string
    {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * Get app url path
     *
     * @param string $currentUrlPath
     * @param string $rootAppUrl
     * @return string
     */
    public static function urlAppPath($currentUrlPath = '', $rootAppUrl = ''): string
    {
        $getPathUrl = str_replace($rootAppUrl, '', $currentUrlPath);
        $getPathUrl = $getPathUrl !== '/' ? wpf_remove_last_slash($getPathUrl) : $getPathUrl;
        return $getPathUrl;
    }

    /**
     * Get full url from the request
     *
     * @return string
     */
    public function getFullUrl()
    {
        // (http or https)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        // get hostname
        $host = $_SERVER['HTTP_HOST'];
        // get URI
        $requestUri = $_SERVER['REQUEST_URI'];
        // set to url
        $url = $scheme . '://' . $host . $requestUri;
        return $url;
    }

    /**
     * Set request items
     *
     * @return \Wpframe\Sys\Traits\Http\HttpRequest
     */
    private function getRequestItem()
    {
        $this->get = isset($_GET) ? (object) $_GET : false;
        $this->post = isset($_POST) ? (object) $_POST : false;
        $this->files = isset($_FILES) ? (object) $_FILES : false;
        return $this;
    }

    /**
     * Validate the method specified in the input form
     *
     * @param boolean|array|string $methodNeddle
     * @return mixed|bool
     */
    private function validateMethodFormInput($methodNeddle = false)
    {
        if (($methodNeddle == false)) return false;
        $this->getRequestItem();

        if (is_array($methodNeddle) && count($methodNeddle) > 0) {
            if (isset($this->post->_inputMethod)) {
                $methodSet = $this->post->_inputMethod;
                if (in_array($methodSet, $methodNeddle)) return $methodNeddle;
            }

            if (!isset($this->post->_inputMethod)) {
                $methodSet = self::requestType();
                if (in_array($methodSet, $methodNeddle)) return $methodNeddle;
            }
        }

        if ((!isset($this->post->_inputMethod))) return false;
        $methodSet = $this->post->_inputMethod;
        if (($methodSet == $methodNeddle)) return $methodNeddle;
        return false;
    }

    /**
     * Request type is "GET"
     *
     * @return boolean
     */
    public function isGet(): bool
    {
        if ((self::requestType() == 'GET')) return true;
        return false;
    }

    /**
     * Request type is "POST"
     *
     * @return boolean
     */
    public function isPost(): bool
    {
        if ((self::requestType() == 'POST')) return true;
        return false;
    }

    /**
     * Request type is "OPTIONS"
     *
     * @return boolean
     */
    public function isOptions(): bool
    {
        if ((self::requestType() == 'OPTIONS')) return true;
        if (($this->validateMethodFormInput('OPTIONS'))) return true;
        return false;
    }

    /**
     * Request type is "PUT"
     *
     * @return boolean
     */
    public function isPut(): bool
    {
        if ((self::requestType() == 'PUT')) return true;
        if (($this->validateMethodFormInput('PUT'))) return true;
        return false;
    }

    /**
     * Request type is "PATCH"
     *
     * @return boolean
     */
    public function isPatch(): bool
    {
        if ((self::requestType() == 'PATCH')) return true;
        if (($this->validateMethodFormInput('PATCH'))) return true;
        return false;
    }

    /**
     * Request type is "DELETE"
     *
     * @return boolean
     */
    public function isDelete(): bool
    {
        if ((self::requestType() == 'DELETE')) return true;
        if (($this->validateMethodFormInput('DELETE'))) return true;
        return false;
    }

    /**
     * Request type is "MATCH"
     *
     * @param array|bool|string $methods
     * @return boolean
     */
    public function isMatch($methods = false): bool
    {
        if (($methods == false)) return false;
        if (!is_array($methods)) return false;
        if (count($methods) == 0) return false;
        if (($this->validateMethodFormInput($methods))) return true;
        return false;
    }

    /**
     * Request type is "ANY"
     *
     * @return boolean
     */
    public function isAny(): bool
    {
        return true;
    }

    /**
     * Get client IP Address
     *
     * @return mixed
     */
    public function getClientIp()
    {
        $serverKey = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($serverKey as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get user agent
     *
     * @return mixed
     */
    public function userAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] !== null) {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return null;
    }

    /**
     * Get All Headers
     *
     * @return array
     */
    public function getAllHeaders()
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                // Konversi HTTP_HEADER_NAME menjadi Header-Name
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get Selected Header
     *
     * @param array $desiredHeaders
     * @return array
     */
    public function getHeaders(array $desiredHeaders = [])
    {
        $allHeaders = $this->getAllHeaders();
        $selectedHeaders = [];
    
        foreach ($desiredHeaders as $header) {
            if (isset($allHeaders[$header])) {
                $selectedHeaders[$header] = $allHeaders[$header];
            }
        }
    
        return $selectedHeaders;
    }
}