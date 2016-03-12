<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 16:53
 */

namespace Framework\Session;


use Framework\ObjectPool;

class Session extends ObjectPool
{
    public $messages = [];

    public function __construct()
    {
        session_start();
    }

    public function __set($name, $val)
    {

    }

    public function __get($name)
    {

    }

    public function addFlush($type, $message)
    {
        $_SESSION['messages'][$type][] = $message;
    }
}