<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 9:29
 */

namespace Framework\Response;

/**
 * Class RedirectResponse
 * Redirects to another uri/url via a Location header.
 *
 * @package Framework\Response
 */
class RedirectResponse extends Response
{
    /**
     * RedirectResponse constructor.
     *
     * @param string $url The url
     * @param string $type Content-Type
     * @param string $method The redirect method
     * @param int $code The redirect status code
     */
    public function __construct($url = '', $type = 'text/html', $method = 'location', $code = 302)
    {
        parent::__construct('', $type, $code);
        $this->setHeader($method, $url);
    }
}