<?php

namespace App\Http\Controllers;
class CURL
{

    const POST = 'post';
    const GET = 'get';
    const PUT = 'put';

    private static $instance = null;
    private $response = false;
    private $error = null;
    private $config = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 120,
    ];
    private $queryString = null;

    public static function init()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function execute( $url = false, $params = [], $config = [], $method = self::POST , $token = null)
    {

        if (!$url) {
            $this->error = "Url is not set";
            return $this;
        }

        if (!is_array($params)) {
            $this->error = "Data must be type of array";
            return $this;
        }

        if (!is_array($config)) {
            $this->error = "Config must be type of array";
            $this->response = false;
        }
        $this->method = $method;
        if (is_array($params) AND count($params)) {
            if ($this->method == self::POST) {
                $this->config[CURLOPT_HTTPGET] = false;
                $this->config[CURLOPT_POST] = true;
                $this->config[CURLOPT_POSTFIELDS] = json_encode($params);
                $token ?
                    $this->config[CURLOPT_HTTPHEADER] = ['Content-Type: application/json' , "AUTHORIZATION: Bearer $token"] :
                    $this->config[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
            } else if ($this->method == self::GET) {
                $this->queryString = http_build_query($params);
                $this->config[CURLOPT_POST] = false;
                $this->config[CURLOPT_HTTPGET] = true;
                $url = $url . "?" . $this->queryString;
            } else if ($this->method == self::PUT) {
                $this->config[CURLOPT_RETURNTRANSFER] = true;
                $this->config[CURLOPT_CUSTOMREQUEST] = "PUT";
                $this->config[CURLOPT_POSTFIELDS] = json_encode($params);
                $token ?
                    $this->config[CURLOPT_HTTPHEADER] = ['Content-Type: application/json' , "AUTHORIZATION: Bearer $token"] :
                    $this->config[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
            }
        }


        if ($config AND count($config)) {
            foreach ($config as $option => $value) {
                $this->config[$option] = $value;
            }
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $this->config);
        $this->response = curl_exec($ch);
        curl_close($ch);

        return $this;
    }

    public function executeWithHeader( $header = [] ,$url = false, $params = [], $config = [], $method = self::POST)
    {
        if (!$url) {
            $this->error = "Url is not set";
            return $this;
        }

        if (!is_array($params)) {
            $this->error = "Data must be type of array";
            return $this;
        }

        if (!is_array($config)) {
            $this->error = "Config must be type of array";
            $this->response = false;
        }
        $this->method = $method;
        if (is_array($params) AND count($params)) {
            if ($this->method == self::POST) {
                $this->config[CURLOPT_HTTPGET] = false;
                $this->config[CURLOPT_POST] = true;
                $this->config[CURLOPT_POSTFIELDS] = json_encode($params);
                $items = ['Content-Type: application/json'];

                if ($header) {
                    foreach ($header as $key => $value) {
                        array_push($items , "$key:$value");
                    }
                }
                $this->config[CURLOPT_HTTPHEADER] = $items;

            } else if ($this->method == self::GET) {
                $this->queryString = http_build_query($params);
                $this->config[CURLOPT_POST] = false;
                $this->config[CURLOPT_HTTPGET] = true;
                $url = $url . "?" . $this->queryString;
            }
        }


        if ($config AND count($config)) {
            foreach ($config as $option => $value) {
                $this->config[$option] = $value;
            }
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->config);
        $this->response = curl_exec($ch);
        curl_close($ch);

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getOptions()
    {
        return $this->config;
    }

    public function response()
    {
        return $this->response;
    }

    public function getError()
    {
        return $this->error;
    }

}