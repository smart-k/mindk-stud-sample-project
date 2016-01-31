<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 27.01.2016
 * Time: 12:20
 */

namespace Framework\Model;


class Model
{

    private $data = array();

    function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

}