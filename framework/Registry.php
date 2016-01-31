<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.01.2016
 * Time: 19:02
 */

namespace Framework;


class Registry
{
    private $data = array();

    function __construct($data = array())
    {
        $this->data = $data;
    }

    function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}