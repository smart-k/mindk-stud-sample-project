<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 16:53
 */

namespace Framework\Session;

use Framework\ObjectPool;
use Framework\DI\Service;


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
        $this->setToken(Service::get('security')->generateFormToken());
    }

    public function __set($name, $val)
    {
        $_SESSION[$name] = $val;
    }

    public function __get($name)
    {
        return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : null;
    }

    /**
     * Set the flush messages in the session.
     *
     * @param $type
     * @param $message
     */
    public function setFlush($type, $message)
    {
        $_SESSION['messages'][$type][] = $message;
    }

    /**
     * Get the flush messages that are stored in the session.
     *
     * @return array
     */
    public function getFlush()
    {
        return isset($_SESSION['messages']) ? $_SESSION['messages'] : [];
    }

    public function clearFlush()
    {
        if (isset($_SESSION['messages'])) {
            unset($_SESSION['messages']);
        }
    }

    /**
     * Store the user data in the session.
     *
     * @param $userdata
     */
    public function setUser($userdata)
    {
        $_SESSION['user'] = serialize($userdata);
    }

    /**
     * Get the user data that are stored in the session.
     *
     * @return object|array|null
     */
    public function getUser()
    {
        return isset($_SESSION['user']) ? unserialize($_SESSION['user']) : null;
    }

    public function clearUser()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
    }

    /**
     * Store the post data in the session.
     *
     * @param $postdata Post
     */
    public function setPost($postdata)
    {
        $_SESSION['post'] = serialize($postdata);
    }

    /**
     * Get the post data that are stored in the session.
     *
     * @return Post|null
     */
    public function getPost()
    {
        return isset($_SESSION['post']) ? unserialize($_SESSION['post']) : null;
    }

    public function clearPost()
    {
        if (isset($_SESSION['post'])) {
            unset($_SESSION['post']);
        }
    }

    /**
     * Write the generated token to the session variable to check it against the hidden field when the form is sent
     *
     * @param string $form Form name. Default behavior - only one token for all forms
     * @param string $token
     */
    public function setToken($token, $form = '')
    {
        $_SESSION[$form . '_token'] = $token;
    }

    public function getToken($form = '')
    {
        return isset($_SESSION[$form . '_token']) ? $_SESSION[$form . '_token'] : null;
    }

    public function clearToken($form = '')
    {
        if (isset($_SESSION[$form . '_token'])) {
            unset($_SESSION[$form . '_token']);
        }
    }

    public function unset_data($name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    public function clear()
    {
        $_SESSION = [];
        session_destroy();
    }

}