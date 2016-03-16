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
        return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
    }

    /**
     * Clear session data.
     */
    public function clear()
    {
        $_SESSION = [];
        session_destroy();
    }
}