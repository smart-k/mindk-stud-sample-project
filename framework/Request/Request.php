<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 08.03.2016
 * Time: 20:22
 */

namespace Framework\Request;

use Framework\ObjectPool;

/**
 * Class Request
 *
 * @package Framework\Request
 */
class Request extends ObjectPool
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() == 'POST');
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return ($this->getMethod() == 'GET');
    }

    /**
     * Get request headers
     *
     * @param string|null $header The header name
     *
     * @return string|null The header value
     */
    public function getHeaders($header = null)
    {

        $data = apache_request_headers();

        if (!empty($header)) {
            $data = array_key_exists($header, $data) ? $data[$header] : null;
        }

        return $data;
    }

    /**
     * Filter request variable
     *
     * @param string $varname
     * @param string $filter_name
     *
     * @return string|null
     */
    public function post($varname = '', $filter_name = 'STRING')
    {
        if ($varname == 'password') {
            return array_key_exists($varname, $_POST) ? md5($this->filter($_POST[$varname], $filter_name)) : null;
        }

        return array_key_exists($varname, $_POST) ? $this->filter($_POST[$varname], $filter_name) : null;
    }

    /**
     * Filter obtained value
     *
     * @param mixed $source
     * @param string $filter_name
     * @return mixed|null
     */
    public function filter($source, $filter_name = 'STRING')
    {
        $result = null;

        switch ($filter_name) {
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