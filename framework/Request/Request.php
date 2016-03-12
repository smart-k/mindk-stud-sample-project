<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 08.03.2016
 * Time: 20:22
 */

namespace Framework\Request;

use Framework\ObjectPool;

class Request extends ObjectPool
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isPost()
    {
        return ($this->getMethod() == 'POST');
    }

    public function isGet()
    {
        return ($this->getMethod() == 'GET');
    }

    public function getHeaders($header = null)
    {

        $data = apache_request_headers();

        if (!empty($header)) {
            $data = array_key_exists($header, $data) ? $data[$header] : null;
        }

        return $data;
    }

    public function post($varname = '', $filter = 'STRING')
    {
        return array_key_exists($varname, $_POST) ? $this->filter($_POST[$varname], $filter) : null;
    }

    protected function filter($source, $filter = 'STRING')
    {
        $result = null;

        switch ($filter) {
            case 'STRING':
                $result = filter_var((string)$source, FILTER_SANITIZE_STRING);
                break;
            case 'EMAIL':
                $result = filter_var((string)$source, FILTER_SANITIZE_EMAIL);
                break;
            case 'INT': // Only use the first integer value
                preg_match('~^\d+~', (string)$source, $matches);
                $result = (int)$matches[0];
                break;
        }

        return $result;

    }

}