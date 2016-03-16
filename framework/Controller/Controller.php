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
    public function __call($methodName, $args = [])
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
     * @param   string $message Message for flush if any
     * @param   int $code Redirect status code
     *
     * @return  ResponseRedirect
     */
    public static function redirect($url, $message = null, $code = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        if (isset($message)) Service::get('session')->addFlush('info', $message);
        return new ResponseRedirect($url, $code);
    }

    /**
     * Generate route.
     *
     * @param string $route_name
     * @param array $params
     *
     * @return string|null
     */
    public function generateRoute($route_name, $params = null)
    {
        return Service::get('router')->buildRoute($route_name, $params);
    }

    /**
     * Rendering method
     *
     * @param   string $layout The layout filename
     * @param   array $data Data
     *
     * @return  Response
     */
    public function render($layout, $data = [])
    {
        $class = get_called_class();

        if ($class === 'Framework\Application') { // When method render has been invoked in Application controller
            $tplPath = Service::get('renderer')->getErrorTemplatePath(); // doing exception rendering
        } else {
            $pos = strrpos($class, '\\');
            $tplPath = Service::get('loader')->getPath($class) . '/../views/' . str_replace('Controller', '', substr($class, $pos + 1)) . '/';
        }
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
