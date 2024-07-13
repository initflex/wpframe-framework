<?php

namespace Wpframe\Sys\Support;

/**
 * Response for applications
 */
class Response
{
    /**
     * Response start
     *
     * @return \Wpframe\Sys\Support\Response
     */
    public static function start()
    {
        return new static;
    }

    /**
     * Set Headers
     *
     * @param array $headers
     * @return void
     */
    public function setHeaders($headers = [])
    {
        function setMultipleHeaders($headers) {
            foreach ($headers as $headerName => $headerValue) {
                $headerString = "$headerName: $headerValue";
                header($headerString);
            }
        }
    }

    /**
     * Get JSON Response
     *
     * @param array $array
     * @return string|mixed
     */
    public function json($array = [], $secondArg = null)
    {
        $this->setHeaders(['Content-Type' => 'application/json']);
        return wpf_to_json($array, $secondArg);
    }

    /**
     * Set Response Code
     *
     * @param int $code
     * @param string $desc
     * @return void
     */
    public function code($code, $desc = '')
    {
        return wpf_status_code($code, $desc);
    }
}