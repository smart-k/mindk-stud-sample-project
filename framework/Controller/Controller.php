<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 15:22
 */

namespace Framework\Controller;

use Framework\Response\RedirectResponse;


class Controller
{

    /**
     * Redirects to another url.
     * Can redirect via a Location header
     *
     * @param   string $url The url
     * @param   string $content The response content
     * @param   int $code The redirect status code
     * @return  object
     */
    public static function redirect($url, $content = '', $code = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        return new RedirectResponse($url, $content, $code);
    }
}