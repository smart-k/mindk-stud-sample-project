<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 18:12
 */

namespace Framework\Security;

use Framework\DI\Service;
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
        $valid_token = false;

        if (isset($_SESSION['token'])) {
            $token = Service::get('request')->filter($_POST['token']);
            $valid_token = ($token == $_SESSION['token']) ? true : false;
        }

        if ($valid_token) {
            return isset($_SESSION['is_authenticated']) ? $_SESSION['is_authenticated'] : false;
        }

        return false;
    }

    /**
     * Set user data that to be stored in the session.
     *
     * @param array|object $user Assoc array, which contains only some of user data (for example email and user role), or whole user class object
     * @param string $user_session_name Name for user data that to be stored in the session
     */
    public function setUser($user, $user_session_name = 'user')
    {
        $_SESSION[$user_session_name] = $user;
        $_SESSION['is_authenticated'] = true;
        $_SESSION['token'] = $this->_random_text();
    }

    /**
     * Get the user data that are stored in the session.
     * @param string $user_session_name Name for user data that are stored in the session
     *
     * @return array|object|null Return user data that are stored in the session (assoc array or whole user class object)
     */
    public function getUser($user_session_name = 'user')
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

    /**
     * @param string $type The alphabet
     * @param int $length The token length (number of characters)
     * @return string The token
     */
    private function _random_text($type = 'alnum', $length = 16)
    {
        switch ($type) {
            case 'alnum':
                $codeAlphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha':
                $codeAlphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'hexdec':
                $codeAlphabet = '0123456789abcdef';
                break;
            case 'numeric':
                $codeAlphabet = '0123456789';
                break;
            case 'nozero':
                $codeAlphabet = '123456789';
                break;
            case 'distinct':
                $codeAlphabet = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                break;
            default:
                $codeAlphabet = (string)$type;
                break;
        }

        $crypto_rand_secure = function ($min, $max) {
            $range = $max - $min;
            if ($range < 0) return $min; // not so random...
            $log = log($range, 2);
            $bytes = (int)($log / 8) + 1; // length in bytes
            $bits = (int)$log + 1; // length in bits
            $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd >= $range);
            return $min + $rnd;
        };

        $token = "";
        $max = strlen($codeAlphabet);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$crypto_rand_secure(0, $max)];
        }

        return $token;
    }
}