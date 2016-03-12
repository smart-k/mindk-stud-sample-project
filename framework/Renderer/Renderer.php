<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 29.02.2016
 * Time: 9:48
 */

namespace Framework\Renderer;


use Framework\ObjectPool;
use Framework\ReflectionMethodNamedArgs;
use Framework\Response\Response;
use Framework\Exception\BadResponseTypeException;
use Framework\DI\Service;

/**
 * Class Renderer
 * @package Framework\Renderer
 */
class Renderer extends ObjectPool
{
    /**
     * @var string  Main wrapper template file location
     */
    protected $_main_template = '';
    protected $_error_template = '';

    /**
     * @return string Path to error template directory
     */
    public function getErrorTemplatePath()
    {
        $pos = strrpos($this->_error_template, '/');
        $template_path = substr($this->_error_template, 0, $pos + 1);
        return $template_path;
    }

    /**
     * Renderer constructor.
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_main_template = $config['main_layout'];
        $this->_error_template = $config['error_500'];
    }

    /**
     * Render main template with specified content
     *
     * @param $content
     *
     * @return html/text
     */
    public function renderMain($content)
    {
        $user = null; // @TODO
        $flush = array();// @TODO
        $route['_name'] = trim($_SERVER['REQUEST_URI'], '/');

        return $this->render($this->_main_template, compact('getRoute', 'user', 'flush', 'content'), false);
    }

    /**
     * Render specified template file with data provided
     *
     * @param   string $template_path Template file path (full)
     * @param   array $data Data array
     * @param   bool    To be wrapped with main template if true
     *
     * @return  text/html $content
     * @throws \Exception If template file not found
     */
    public function render($template_path, $data = array(), $wrap = true)
    {
        /**
         * Closure for ../src/views/Post/index.html.php template
         *
         * @param string $controller_name
         * @param string $action
         * @param array $data
         * @throws \Exception If obtained response is not instance of Response.
         */
        $include = function ($controller_name, $action, $data = array()) {

            $response = Service::get('application')->getActionResponse($controller_name, $action, $data);

            if ($response instanceof Response) {
                echo htmlspecialchars_decode($response->content);
            } else {
                throw new BadResponseTypeException('Response type not known');
            }
        };

        /**
         * Closure for login.html.php, signin.html.php and layout.html.php templates
         *
         * @param string $route_name
         * @param array $params
         * @return string|null
         */
        $getRoute = function ($route_name, $params = null) {
            return Service::get('router')->buildRoute($route_name, $params);
        };

        extract($data);

        if (file_exists($template_path)) {
            ob_start();
            include($template_path);
            $content = ob_get_clean();
        } else {
            throw new \Exception('File ' . $template_path . ' not found');
        }

        if ($wrap) {
            if (file_exists($this->_main_template)) {
                $content = $this->renderMain($content);
            } else {
                throw new \Exception('File ' . $this->_main_template . ' not found');
            }
        }
        return $content;
    }
}