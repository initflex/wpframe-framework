<?php

namespace Wpframe\Sys\Http;

use DateTime;
use Wpframe\Sys\Traits\Http\HttpRequest;

/**
 * Handles HTTP requests
 */
class Request
{
    use HttpRequest;

    public $urlSecCheckResults = false;
    public $httpDataUrlSecCheck = [];
    private static $appConfig = [];
    private static $securityConfig = [];

    /**
     * Set http request item to object
     * 
     * @return void
     */
    public function __construct()
    {
        $this->getRequestItem();
    }

    /**
     * Initialize the request
     *
     * @return \Wpframe\Sys\Http\Request
     */
    public static function start()
    {
        self::$appConfig = wpf_config('app') ? wpf_config('app') : [];
        self::$securityConfig = wpf_config('security') ? wpf_config('security') : [];
        return new static;
    }

    /**
     * Handle http requests
     *
     * @return void
     */
    public function requestHandler()
    {
        $this->httpDataUrlSecCheck = $this->urlSecurityCheck();
        $urlRequestStatus = $this->urlSecCheckResults ? 'blocked' : 'passed';
        $this->requestCaptureToFile($this->httpDataUrlSecCheck);

        if($this->urlSecCheckResults){
            add_action('wp_head', function() {
                wpf_redirect_url(site_url('/')); 
            }, 0);
        }
    }

    /**
     * Do string comparison for url filter
     *
     * @param string $stringToSearch
     * @param string $neddle
     * @return bool
     */
    private function urlStringInPos($stringToSearch = '', $neddle = ''): bool
    {
        if ($stringToSearch && $neddle == '') return false;
        return  strpos($stringToSearch, $neddle) !== false &&
            strpos($stringToSearch, $neddle) >= 0 ? true : false;
    }

    /**
     * Filter http requests based on patterns
     *
     * @param string $urlFilterItem
     * @param string $getRequestUrl
     * @param array $urlFilterItemConf
     * @return string break|continue
     */
    private function checkUrlSecString($urlFilterItem = '', $getRequestUrl = '', $urlFilterItemConf = [])
    {
        if ($this->urlStringInPos($getRequestUrl, $urlFilterItem)) {
            if ($this->whitelistUrlFilterPerItem($urlFilterItemConf, $getRequestUrl))  return 'continue';
            if ($this->whitelistUrlFilterAll($getRequestUrl))  return 'continue';
            $this->urlSecCheckResults = true;
            return 'break';
        } else {
            return 'continue';
        }
    }

    /**
     * Filter Http requests with regex method
     *
     * @param string $urlFilterItem
     * @param string $getRequestUrl
     * @param array $urlFilterItemConf
     * @return string break|continue
     */
    private function checkUrlSecRegex($urlFilterItem = '', $getRequestUrl = '', $urlFilterItemConf = [])
    {
        $getPregmatchPattern = explode('regex:', trim($urlFilterItem));
        $setRegexPatern = isset($getPregmatchPattern[1]) ? trim($getPregmatchPattern[1]) : null;
        if ($setRegexPatern !== null && preg_match($setRegexPatern, $getRequestUrl)) {
            if ($this->whitelistUrlFilterPerItem($urlFilterItemConf, $getRequestUrl))  return 'continue';
            if ($this->whitelistUrlFilterAll($getRequestUrl))  return 'continue';
            $this->urlSecCheckResults = true;
            return 'break';
        } else {
            return 'continue';
        }
    }

    /**
     * Check the pattern whitelist for global configuration
     *
     * @param string $getRequestUrl
     * @return bool false = url not whitelisted
     * @return bool true = url whitelisted
     */
    private function whitelistUrlFilterAll($getRequestUrl = ''): bool
    {
        // false = url not whitelisted
        // true = url whitelisted
        if (!isset(self::$securityConfig['url_filter_list']['whitelist_urls'])) return false;
        $whitelistUrlsAll = self::$securityConfig['url_filter_list']['whitelist_urls'];
        if (count($whitelistUrlsAll) > 0) {
            foreach ($whitelistUrlsAll as $item) {
                $checkItemIsRegex = strlen(trim($item)) > 0 ?
                    ($this->urlStringInPos($item, 'regex:') ? true : false) : false;
                if (!$checkItemIsRegex) {
                    if ($this->checkkUrlWhitelistString($getRequestUrl, $item)) return true;
                    continue;
                } else {
                    if ($this->checkkUrlWhitelistRegex($getRequestUrl, $item)) return true;
                    continue;
                }
            }
        }
        return false;
    }

    /**
     * Check the pattern whitelist per item
     *
     * @param array $urlFilterItemConf
     * @param string $getRequestUrl
     * @return bool false = url not whitelisted
     * @return bool true = url whitelisted
     */
    private function whitelistUrlFilterPerItem($urlFilterItemConf = [], $getRequestUrl = ''): bool
    {
        // false = url not whitelisted
        // true = url whitelisted
        if (!isset($urlFilterItemConf['whitelist_urls'])) return false;
        if (count($urlFilterItemConf) > 0) {
            foreach ($urlFilterItemConf['whitelist_urls'] as $item) {
                $checkItemIsRegex = strlen(trim($item)) > 0 ?
                    ($this->urlStringInPos($item, 'regex:') ? true : false) : false;
                if (!$checkItemIsRegex) {
                    if ($this->checkkUrlWhitelistString($getRequestUrl, $item)) return true;
                    continue;
                } else {
                    if ($this->checkkUrlWhitelistRegex($getRequestUrl, $item)) return true;
                    continue;
                }
            }
        }
        return false;
    }

    /**
     * Check whitelist string
     *
     * @param string $getRequestUrl
     * @param string $item
     * @return bool
     */
    private function checkkUrlWhitelistString($getRequestUrl = '', $item = ''): bool
    {
        if ($getRequestUrl == $item) return true;
        return false;
    }

    /**
     * Check the regex whitelist to filter Http requests
     *
     * @param string $getRequestUrl
     * @param string $item
     * @return bool
     */
    private function checkkUrlWhitelistRegex($getRequestUrl = '', $item = ''): bool
    {
        $getPregmatchPattern = explode('regex:', trim($item));
        $setRegexPatern = isset($getPregmatchPattern[1]) ? trim($getPregmatchPattern[1]) : null;
        if ($setRegexPatern !== null && preg_match($setRegexPatern, $getRequestUrl)) return true;
        return false;
    }

    /**
     * Filter URLs from Http requests
     *
     * @return array
     */
    public function urlSecurityCheck()
    {
        $getRequestUrl = urldecode(
            self::urlAppPath(wpf_get_domain_url() . $_SERVER['REQUEST_URI'], self::$appConfig)
        );
        $getItemFilterTmp = '';

        if (!self::$securityConfig['url_filter_enable']) return [
            'url' => self::urlAppPath(wpf_get_domain_url() . $_SERVER['REQUEST_URI'], self::$appConfig),
            'http_status_code' => http_response_code(),
            'url_filter_status' => (self::$securityConfig['url_filter_enable'] ? 'enabled' : 'disabled'),
            'url_filter_msg' => ($this->urlSecCheckResults ? $getItemFilterTmp : 'null'),
            'request_method' => self::requestType(),
            'request_method_input' => (isset($this->post->_inputMethod) ? $this->post->_inputMethod : 'null'),
            'request_status' => ($this->urlSecCheckResults ? 'blocked' : 'passed'),
            'client_ip' => $this->getClientIp(),
            'request_id' => wpf_unique_id(30),
            'user_agent' => $this->userAgent(),
        ];

        foreach (self::$securityConfig['url_filter_list']['url_filter_patterns'] as $urlFilterItem => $urlFilterItemConf) {
            $getItemFilterTmp = $urlFilterItem;
            $checkItemIsRegex = strlen(trim($urlFilterItem)) > 0 ?
                ($this->urlStringInPos($urlFilterItem, 'regex:') ? true : false) : false;
            if (strlen(trim($urlFilterItem)) <= 0) continue;
            if (!$checkItemIsRegex) {
                $checkUrlSecStringRes = $this->checkUrlSecString($urlFilterItem, $getRequestUrl, $urlFilterItemConf);
                if ($checkUrlSecStringRes == 'break') break;
                if ($checkUrlSecStringRes == 'continue') continue;
            } else {
                $checkUrlSecRegexRes = $this->checkUrlSecRegex($urlFilterItem, $getRequestUrl, $urlFilterItemConf);
                if ($checkUrlSecRegexRes == 'break') break;
                if ($checkUrlSecRegexRes == 'continue') continue;
            }
        }

        return [
            'url' => self::urlAppPath(wpf_get_domain_url() . $_SERVER['REQUEST_URI'], self::$appConfig),
            'http_status_code' => http_response_code(),
            'url_filter_status' => (self::$securityConfig['url_filter_enable'] ? 'enabled' : 'disabled'),
            'url_filter_msg' => ($this->urlSecCheckResults ? $getItemFilterTmp : 'null'),
            'request_method' => self::requestType(),
            'request_method_input' => (isset($this->post->_inputMethod) ? $this->post->_inputMethod : 'null'),
            'request_status' => ($this->urlSecCheckResults ? 'blocked' : 'passed'),
            'client_ip' => $this->getClientIp(),
            'request_id' => wpf_unique_id(30),
            'user_agent' => $this->userAgent(),
        ];
    }

    /**
     * Create a log from Http filter
     *
     * @param array $urlSecCheckData
     * @return mixed
     */
    private function writeContentUrlFilterLog($urlSecCheckData = [])
    {
        if (is_array($urlSecCheckData) && count($urlSecCheckData) > 0) {
            $dateTime = new DateTime();
            $timestamp = $dateTime->format('Y-m-d\TH:i:s.uP');
            $getFileContentHttpLog = file_get_contents(self::$securityConfig['http_log_file']);
            $setHttpRequestMsg = $getFileContentHttpLog . 
                (strlen(trim($getFileContentHttpLog)) > 0 ? "\r\n" : "") 
                . $timestamp ." | Request ID: ". $urlSecCheckData['request_id'] 
                . " | URL: ". $urlSecCheckData['url'] 
                . " | Method: ". $urlSecCheckData['request_method']
                . " | Input Method: ". $urlSecCheckData['request_method_input'] 
                . " | URL Filter Status: ". $urlSecCheckData['url_filter_status'] 
                . " | Ip: ". $urlSecCheckData['client_ip'] 
                . " | Request Status: ". $urlSecCheckData['request_status'] 
                . " | Http Status Code: ". $urlSecCheckData['http_status_code'] 
                . " | User Agent: ". $urlSecCheckData['user_agent']  
                . " | URL Filter Msg: ". ($urlSecCheckData['url_filter_msg'] == 'null' ? 
                    '-' : 'Blocked by URL Filter ('. $urlSecCheckData['url_filter_msg'] .")");

            return wpf_write_file(self::$securityConfig['http_log_file'], $setHttpRequestMsg, 'w');
        }
        return false;
    }

    /**
     * Get http request information and log it
     *
     * @param array $urlSecCheckData
     * @return mixed
     */
    private function requestCaptureToFile($urlSecCheckData = [])
    {
        // make log file http request
        if (!file_exists(self::$securityConfig['http_log_file'])) {
            wpf_write_file(self::$securityConfig['http_log_file'], '', 'w');
        }
        // put http request to log file
        if(self::$securityConfig['url_filter_write_log_enable'] && !self::$securityConfig['http_write_log_enable']) {
            return $this->writeContentUrlFilterLog($urlSecCheckData);
        }
        if(self::$securityConfig['http_write_log_enable']) {
            return $this->writeContentUrlFilterLog($urlSecCheckData);
        }
        return false;
    }
}