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
     * Redirects to another url.
     * Can redirect via a Location header
     *
     * @param   string $url The url
     * @param   string $content The response content
     * @param   int $code The redirect status code
     * @return  object
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
        return Router::getInstance()->buildRoute($route_name, $params);
    }

    /**
     * Rendering method
     *
     * @param   string $layout Layout file name
     * @param   string $main_template_file
     * @param   mixed $data Data
     *
     * @return  Response
     */
    function render($layout, $data = array())
    {
        $class = get_called_class();

        if ($class === 'Framework\Application') {
            $tplPath = dirname(__FILE__) . '/../../src/Blog/views/';
            $main_template = $tplPath;
        } else {
            $main_template = ObjectPool::getInstance('Framework\Loader')->getPath($class) . '/../views/';
            $tplPath = $main_template . str_replace('Controller', '', $class) . '/';
        }
        $template_path = realpath($main_template . 'layout.html.php');
        $full_path = realpath($tplPath . $layout . '.php');

        $renderer = Renderer::getInstance(); // Try to define renderer like a service. e.g.: Service::get('renderer');

        $content = $renderer->setMainTemplate($template_path)->render($full_path, $data, false);

        return new Response($content);
    }

}