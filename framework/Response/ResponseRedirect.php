<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 9:29
 */

namespace Framework\Response;

/**
 * Class ResponseRedirect
 * Redirects to another uri/url via a Location header.
 *
 * @package Framework\Response
 */
class ResponseRedirect extends Response
{
    /**
     * ResponseRedirect constructor.
     * Set the Location header.
     *
     * @param string $url The url
     * @param string $content The response content
     * @param int $code The redirect status code
     * @param string $type Content-Type
     */
    public function __construct($url, $content = '', $code = 302, $type = 'text/html')
    {
        parent::__construct($content, $code, $type);
        $this->setHeader('Location', $url);
    }
}
