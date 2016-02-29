<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 15:22
 */

namespace Framework\Controller;

use Framework\Response\ResponseRedirect;
use Framework\Response\Response;
use Framework\Router\Router;
use Framework\ObjectPool;
use Framework\Renderer\Renderer;


abstract class Controller
{
    function __call($methodName, $args = array())
    {
        if (method_exists($this, $methodName))
            return call_user_func_array(array($this, $methodName), $args);
        else
            throw new \Exception('In controller ' . get_called_class() . ' method ' . $methodName . ' not found!');
    }

    /**
     * Redirect to another url.
     * Can redirect via a Location header
     *
     * @param   string $url The url
     * @param   string $content The response content
     * @param   int $code The redirect status code
     * @return  ResponseRedirect
     */
    static function redirect($url, $content = '', $code = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        return new ResponseRedirect($url, $content, $code);
    }

    /**
     * Generate route.
     *
     * @param string $route_name
     * @param array $params
     * @return string|null
     */
    function generateRoute($route_name, $params = null)
    {
        return Router::get()->buildRoute($route_name, $params);
    }

    /**
     * Rendering method
     *
     * @param   string $layout Layout filename
     * @param   string $main_template_file
     * @param   mixed $data Data
     *
     * @return  Response
     */
    function render($layout, $data = array())
    {
        $class = get_called_class();

        if ($class === 'Framework\Application') {  // Exception rendering (render has been invoked from Application)
            $tplPath = dirname(__FILE__) . '/../../src/Blog/views/';
        } else {
            $tplPath = ObjectPool::get('Framework\Loader')->getPath($class) .
                '/../views/' . str_replace('Controller', '', $class) . '/';
        }

        $full_path = realpath($tplPath . $layout . '.php');
// Try to define renderer like a service. e.g.: Service::get('renderer');
        $content = ObjectPool::get('Framework\Renderer\Renderer',
            include(__DIR__ . '/../../app/config/config.php'))->render($full_path, $data, false);
        return new Response($content);
    }

}