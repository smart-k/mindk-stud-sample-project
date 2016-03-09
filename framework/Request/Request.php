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

    protected $_request_method;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->_request_method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return true if $_SERVER['REQUEST_METHOD'] is POST
     */
    public function isPost()
    {
        return $this->_request_method === 'POST';
    }

    /**
     * Find and receive the request parameter by a key
     *
     * @param string $key
     * @return mixed
     */
    public function post($key)
    {
        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : null;
    }
}