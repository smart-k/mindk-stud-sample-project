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
 * @package Framework\Security
 */
class Security extends ObjectPool
{
    /**
     * Check if the user is authenticated
     * @return bool True if authenticated
     */
    public function isAuthenticated()
    {
        if (isset($_SESSION["is_authenticated"])) {
            return $_SESSION["is_authenticated"];
        } else return false;
    }

    /**
     * Set authenticated user.
     *
     * @param object $user
     */
    public function setUser($user)
    {
        $_SESSION["user"] = $user;
        $_SESSION["is_authenticated"] = true;
    }

    /**
     * Get authenticated user.
     */
    public function getUser()
    {
        if (isset($_SESSION["user"])) {
            return $_SESSION["user"];
        } else return null;
    }

    /**
     * Clear session data.
     */
    public function clear()
    {
        $_SESSION = array();
        session_destroy();
    }
}