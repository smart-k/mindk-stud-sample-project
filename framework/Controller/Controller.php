<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 15:22
 */

namespace Framework\Controller;

use Framework\Response\Response;
use Framework\Response\ResponseRedirect;
use Framework\DI\Service;


/**
 * Class Controller
 *
 * @package Framework\Controller
 */
abstract class Controller
{
    public function __call($methodName, Array $args = [])
    {
        if (method_exists($this, $methodName))
            return call_user_func_array(array($this, $methodName), $args);
        else
            throw new \Exception('In controller ' . get_called_class() . ' method ' . $methodName . ' not found!');
    }

    /**
     * Redirect to specified URL via a Location header.
     *
     * @param   string $url The URL to redirect
     * @param   string|null $message The message for flush if any
     * @param   int $code The redirect status code
     *
     * @return  ResponseRedirect
     */
    public static function redirect($url, $message = null, $code = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        if (isset($message)) Service::get('session')->setFlush('info', $message);
        return new ResponseRedirect($url, $code);
    }

    /**
     * Generate route.
     *
     * @param string $route_name
     * @param array|null $params
     *
     * @return string|null
     */
    public function generateRoute($route_name, Array $params = [])
    {
        return Service::get('router')->buildRoute($route_name, $params);
    }

    /**
     * Render data
     *
     * @param   string $layout The layout filename
     * @param   array $data The data
     *
     * @return  Response
     */
    public function render($layout, Array $data = [])
    {
        $class = get_called_class();
        $pos = strrpos($class, '\\');
        $tplPath = Service::get('loader')->getPath($class) . '/../views/' . str_replace('Controller', '', substr($class, $pos + 1)) . '/';
        $full_path = realpath($tplPath . $layout . '.php');
        $content = Service::get('renderer')->render($full_path, $data);
        return new Response($content);
    }

    /**
     * Get Request instance
     *
     * @return null|Request
     */
    public function getRequest()
    {
        return Service::get('request');
    }

}
