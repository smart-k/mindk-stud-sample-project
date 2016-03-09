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

    protected $request_method;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->request_method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if $_SERVER['REQUEST_METHOD'] is POST
     *
     * @return bool
     */
    public function isPost()
    {
        if ($this->request_method === 'POST') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Find and receive the request parameter by a key
     *
     * @param string $key
     * @return mixed
     */
    public function post($key)
    {
        if (array_key_exists($key, $_REQUEST)) {

            return $_REQUEST[$key];
        }
        return null;
    }
}