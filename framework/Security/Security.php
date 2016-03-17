<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 18:12
 */

namespace Framework\Security;

use Framework\ObjectPool;


/**
 * Class Security
 *
 * @package Framework\Security
 */
class Security extends ObjectPool
{
    /**
     * Check if the user is authenticated
     *
     * @return bool True if the user is authenticated
     */
    public function isAuthenticated()
    {
        return isset($_SESSION["is_authenticated"]) ? $_SESSION["is_authenticated"] : false;
    }

    /**
     * Set user data.
     *
     * @param array|object $user Assoc array, which contains only some of user data (for example email and user role), or instance of whole user class
     * @param string $user_session_name Name for user data saved in session
     */
    public function setUser($user, $user_session_name = "user")
    {
        $_SESSION[$user_session_name] = $user;
        $_SESSION["is_authenticated"] = true;
    }

    /**
     * Get user data.
     * @param string $user_session_name Name for user data saved in session
     *
     * @return array|object|null Return user data saved in session (assoc array of user data or instance of whole user class)
     */
    public function getUser($user_session_name = "user")
    {
        return isset($_SESSION[$user_session_name]) ? $_SESSION[$user_session_name] : null;
    }

    /**
     * Clear session data when user logout.
     */
    public function clear()
    {
        $_SESSION = [];
        session_destroy();
    }
}