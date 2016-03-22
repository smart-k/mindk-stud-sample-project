<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 16:53
 */

namespace Framework\Session;

use Framework\DI\Service;
use Framework\ObjectPool;

/**
 * Class Session
 *
 * @package Framework\Session
 */
class Session extends ObjectPool
{
    public function __construct()
    {
        session_start();
        Service::get('security')->generateFormToken();
    }

    public function __set($name, $val)
    {
        $_SESSION[$name] = $val;
    }

    public function __get($name)
    {
        return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : null;
    }

    public function addFlush($type, $message)
    {
        $_SESSION['messages'][$type][] = $message;
    }

    public function addUser($userdata)
    {
        $_SESSION['user'] = $userdata;
    }

    public function unset_data(Array $names = [])
    {
        foreach ($names as $item) {
            unset($_SESSION[$item]);
        }
    }

    public function clear()
    {
        $_SESSION = [];
        session_destroy();
    }

}