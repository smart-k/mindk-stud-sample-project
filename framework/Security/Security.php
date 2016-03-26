<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 18:12
 */

namespace Framework\Security;

use Framework\ObjectPool;
use Framework\DI\Service;


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
        $auth = $this->verifyFormToken();

        Service::get('session')->clearSessionToken();

        $token = Service::get('security')->generateFormToken();

        Service::get('session')->setSessionToken($token);
        Service::get('session')->setCookieToken($token);


        return $auth == true ? Service::get('session')->is_authenticated : false;
    }

    /**
     * Generate form token
     *
     * @param string $type The alphabet
     * @param int $length The token length (number of characters)
     *
     * @return string The token
     */
    public function generateFormToken($type = 'alnum', $length = 16)
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

    /**
     * Check if the form token is valid
     *
     * @param string $form Form name. The default behavior - only one token for all forms (form name is not in use)
     *
     * @return bool True if form token is valid
     */
    public function verifyFormToken($form = '')
    {
        if (empty($post_token = Service::get('session')->getSessionToken($form))) { // Check if session is started and token is transmitted, if not return an error
            return false;
        }

        if (empty($_POST['token'])) { // Check if form is sent with token in it
            return false;
        }

        $cookie_token = Service::get('session')->getCookieToken($form);
        $session_token = Service::get('session')->getSessionToken($form);

        if (empty($cookie_token)) {
            return $session_token == $post_token; // Compare the tokens against each other if they are still the same
        } else {
            return $session_token == $post_token ? ($cookie_token == $session_token) : false;
        }
    }

    /**
     * Set authenticated status "true".
     * Store user data in the session.
     *
     * @param array|object $user Assoc array, which contains only some of user data (for example email and user role), or whole user class object
     */
    public function setUser($user)
    {
        Service::get('session')->setUser($user);
        Service::get('session')->is_authenticated = true;
    }

    /**
     * Clear session data when user logout.
     */
    public function clear()
    {
        Service::get('session')->clear();
    }


}