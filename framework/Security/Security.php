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
        if ($this->verifyFormToken() == false) {
            return false;
        }
        return Service::get('session')->is_authenticated ?: false;
    }

    /**
     * Generate form token
     *
     * @param string $form The form name
     * @param string $type The alphabet
     * @param int $length The token length (number of characters)
     *
     * @return string The token
     */
    public function generateFormToken($form = '', $type = 'alnum', $length = 16)
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

        $_SESSION[$form . '_token'] = $token; // Write the generated token to the session variable to check it against the hidden field when the form is sent

        return $token;
    }

    /**
     * Check if the form token is valid
     *
     * @param string $form Form name. The default behavior - only one token for all forms (form name is not in use)
     *
     * @return bool True if form token is valid
     */
    public function verifyFormToken($form = '')
    {
        if (!Service::get('session')->__get($form . '_token')) { // Check if a session is started and a token is transmitted, if not return an error
            return false;
        }

        if (empty($_POST['token'])) { // Check if the form is sent with token in it
            return false;
        }
        $token = Service::get('request')->filter($_POST['token']);
        if (Service::get('session')->__get($form . '_token') !== $token) { // Compare the tokens against each other if they are still the same
            return false;
        }
        return true;
    }

    /**
     * Set user data that to be stored in the session.
     *
     * @param array|object $user Assoc array, which contains only some of user data (for example email and user role), or whole user class object
     */
    public function setUser($user)
    {
        Service::get('session')->addUser(serialize($user));
        Service::get('session')->is_authenticated = true;
    }

    /**
     * Get the user data that are stored in the session.
     *
     * @return array|object|null Return user data that are stored in the session (assoc array or whole user class object)
     */
    public function getUser()
    {
        $user = null;

        if (Service::get('session')->user) {
            $user = unserialize(Service::get('session')->user);
        }

        return $user;
    }

    /**
     * Clear session data when user logout.
     */
    public function clear()
    {
        Service::get('session')->clear();
    }


}