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
    const SESSION_LIFE_TIME = 3600;
    const SESSION_NAME = 'SES';

    public function __construct()
    {
        $this->_startSession();
        if (empty($this->getSessionToken())) {
            $token = Service::get('security')->generateFormToken();
            $this->setSessionToken($token);
            $this->setCookieToken($token);
        }
    }

    /**
     * Start session
     *
     * @param int $time The session lifetime in seconds
     * @param string $ses The session name
     */
    private function _startSession($time = self::SESSION_LIFE_TIME, $ses = self::SESSION_NAME)
    {
        ini_set('php_flag session.cookie_httponly', 'on');
        session_set_cookie_params($time);
        session_name($ses);
        session_start();

        // Reset the expiration time upon page load
        if (isset($_COOKIE[$ses])) {
            setcookie($ses, $_COOKIE[$ses], time() + $time, "/");
        }
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
    public function setSessionToken($token, $form = '')
    {
        $_SESSION[$form . '_token'] = $token;
    }

    /**
     * Write the generated token to the cookie variable to check it against the hidden field when the form is sent
     *
     * @param string $form Form name. Default behavior - only one token for all forms
     * @param string $token
     */
    public function setCookieToken($token, $form = '')
    {
        setcookie($form . '_token', $token);
    }

    public function getSessionToken($form = '')
    {
        return isset($_SESSION[$form . '_token']) ? $_SESSION[$form . '_token'] : null;
    }

    public function getCookieToken($form = '')
    {
        return isset($_COOKIE[$form . '_token']) ? $_COOKIE[$form . '_token'] : null;
    }

    public function clearSessionToken($form = '')
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

    public function clear($form = '')
    {
        $_SESSION = [];
        session_destroy();

        setcookie($form . '_token', "", 1); // Set the expiration date to 1-st January 1970
    }

}